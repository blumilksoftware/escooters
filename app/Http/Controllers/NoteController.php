<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $notes = Note::where("user_id", $user->id)->orderBy("created_at", "desc")->get();

        return Inertia::render("Notes/Index", [
            "notes" => $notes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $notes = $request->input("noteText", []);

        if (!is_array($notes)) {
            $notes = [$notes];
        }

        foreach ($notes as $note) {
            if ($note !== null) {
                $user->notes()->create(["text" => $note]);
            }
        }

        return redirect()->back();
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return redirect()
            ->back()
            ->with("success", __("Note has been deleted"));
    }
}
