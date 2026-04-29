<?php

namespace Tests;

use App\Models\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function assertDatabaseHasOne(string $model, array $attributes): void
    {
        $this->assertDatabaseHas($model, $attributes);
        $this->assertDatabaseCount($model, 1);
    }

    protected function assertDatabaseHasMany(string $model, array|Collection $attributes): void
    {
        foreach ($attributes as $attribute) {
            $this->assertDatabaseHas($model, $attribute);
        }

        $this->assertDatabaseCount($model, count($attributes));
    }

    /**
     * Assert that a given where condition exists in the database.
     *
     * @param  iterable<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|class-string<\Illuminate\Database\Eloquent\Model>|string  $table
     * @param  array<string, mixed>  $data
     * @param  string|null  $connection
     * @return $this
     */
    protected function assertDatabaseHas($table, array|Collection $data = [], $connection = null): self {
        $attributes = Arr::only($data, (new $table)->getFillable() ?? []);
        return parent::assertDatabaseHas($table, $attributes, $connection);
    }
}
