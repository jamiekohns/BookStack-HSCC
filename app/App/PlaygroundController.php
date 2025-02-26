<?php

namespace BookStack\App;

use Illuminate\Http\Request;
use BookStack\Http\Controller;
use Illuminate\Support\Facades\DB;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\Auth;
use BookStack\Util\SimpleListOptions;
use BookStack\Activity\ActivityQueries;
use Illuminate\Support\Facades\Redirect;
use BookStack\Entities\Models\Playground;
use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Queries\QueryTopFavourites;
use BookStack\Entities\Queries\QueryRecentlyViewed;

class PlaygroundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()) {
                Redirect::to('login')->send();
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->hasRole(5)) {
            $playgrounds = Playground::orderBy('updated_at', 'desc')->get();
        } else {
            $playgrounds = Playground::where(function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->orWhere('published', true);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        }
// dd($playgrounds->first()->source);
        return view('playground.index')
            ->with('playgrounds', $playgrounds);
    }

    public function new()
    {
        return view('playground.new');
    }

    public function view(Request $request, int $id)
    {
        $playground = Playground::with('user')->find($id);
        $user = Auth::user();
        if ($user->id !== $playground->user_id && !$user->hasRole(5)) {
            $this->showWarningNotification('You cannot view a playground owned by another user.');
            return redirect()->route('playground');
        }

        $data = json_decode(str_replace("\n", '\n', $playground->data));
        $edit = Auth::user()->id == $playground->user_id;

        return view('playground.view')
            ->with('playground', $playground)
            ->with('edit', $edit)
            ->with('content', $data);
    }

    public function getContent(Request $request, int $id)
    {
        $playground = Playground::find($id);
        $data = json_decode(str_replace("\n", '\n', $playground->data));

        return response()->json($data);
    }

    public function create(Request $request)
    {
        $playground = new Playground();

        $playground->name = $request->input('name');
        $playground->data = $request->input('content', '{}');
        $playground->user_id = Auth::user()->id;
        $playground->locked = (bool) $request->input('locked', false);
        $playground->published = (bool) $request->input('published', false);
        if ($request->input('source_id')) {
            $playground->source_id = $request->input('source_id');
        }

        $playground->save();

        return redirect()->action([PlaygroundController::class, 'view'], ['id' => $playground->id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $playground = Playground::find($id);
        $data = $request->input('content');
        $playground->data = $data;

        if ($playground->user_id == Auth::user()->id || Auth::user()->hasRole(5)) {
            $playground->save();

            return response()->json([
                'id' => $playground->id,
                'locked' => $playground->locked,
                'published' => $playground->published,
            ]);
        } else {
            return response()->json([
                'error' => 'pg not owned',
            ], 401);
        }

        return response()->json([
            'error' => 'unkown',
        ], 422);
    }

    public function toggle(Request $request, int $id)
    {
        $playground = Playground::find($id);

        if (!Auth::user()->hasRole(5)) {
            if ($playground->user_id !== Auth::user()->id) {
                return response()->json([
                    'error' => 'pg not owned',
                ], 401);
            }
        }

        $published = $request->input('published');
        if ($published) {
            $playground->published = !$playground->published;
        }

        $locked = $request->input('locked');
        if ($locked) {
            $playground->locked = !$playground->locked;
        }

        $playground->save();

        // return redirect()->action([PlaygroundController::class, 'index']);
        return response()->json([
            'playground' => $playground->toJson()
        ]);
    }

    public function copy(Request $request, $id)
    {
        $playground = Playground::find($id);
        $name = $request->input('name') ?? $playground->name . ' [COPY]';

        return $this->clone($playground, $name);
    }

    public function clone(Playground $playground, string $name)
    {
        $userId = Auth::user()->id;

        // only owners can make more than one copy
        // look for previous copy and redriect if one exists
        if ($playground->user_id !== $userId) {
            $previousCopy = Playground::where('user_id', $userId)
                ->where('source_id', $playground->id)
                ->first();

            if ($previousCopy) {
                $this->showWarningNotification('You have already copied this playground. You have been redirected to your copy.');
                return redirect()->action([PlaygroundController::class, 'view'], ['id' => $previousCopy->id]);
            }
        }

        $playgroundClone = $playground->replicate();
        $playgroundClone->user_id = Auth::user()->id;
        $playgroundClone->locked = false;
        $playgroundClone->published = false;
        $playgroundClone->name = $name;
        $playgroundClone->source_id = $playground->id;

        $playgroundClone->save();

        return redirect()->action([PlaygroundController::class, 'view'], ['id' => $playgroundClone->id]);
    }

    public function delete(Request $request, int $id) 
    {
        $playground = Playground::find($id);

        if ($playground->user_id !== Auth::user()->id) {
            $this->ErrorNotification('You cannot delete playgrounds that you do not own');
            return redirect()->action([PlaygroundController::class, 'view'], ['id' => $playgroundClone->id]);
        }

        $playground->delete();

        return redirect()->action([PlaygroundController::class, 'index']);
    }
}
