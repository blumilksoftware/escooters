<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class NotesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testExample(): void
    {
        $response = $this->get("/");

        $response->assertStatus(200);
    }

    public function testNotesAreStoredInDatabase(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $noteData = [
            "text" => "test",
        ];

        $this->post("/notes", $noteData);

        $this->assertDatabaseHas("notes", $noteData);
    }
}
