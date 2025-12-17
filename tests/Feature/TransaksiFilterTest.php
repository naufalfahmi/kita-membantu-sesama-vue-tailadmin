<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Transaksi;
use App\Models\User;

class TransaksiFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_date_range_filter_returns_expected_records()
    {
        $user = User::factory()->create();

        Transaksi::create(['nominal' => 100, 'tanggal_transaksi' => '2025-12-10', 'created_by' => $user->id]);
        Transaksi::create(['nominal' => 200, 'tanggal_transaksi' => '2025-12-15', 'created_by' => $user->id]);
        Transaksi::create(['nominal' => 300, 'tanggal_transaksi' => '2025-12-20', 'created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/admin/api/transaksi?tanggal_from=2025-12-12&tanggal_to=2025-12-18');

        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('2025-12-15', $data[0]['tanggal_transaksi']);
    }

    public function test_single_date_filter_returns_expected_records()
    {
        $user = User::factory()->create();
        Transaksi::create(['nominal' => 123, 'tanggal_transaksi' => '2025-12-25', 'created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/admin/api/transaksi?tanggal=2025-12-25');
        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('2025-12-25', $data[0]['tanggal_transaksi']);
    }

    public function test_same_day_range_is_inclusive()
    {
        $user = User::factory()->create();
        Transaksi::create(['nominal' => 10, 'tanggal_transaksi' => '2025-12-12', 'created_by' => $user->id]);

        // debug: replicate controller query to inspect SQL and results
        $query = \App\Models\Transaksi::with(['donatur:id,nama']);
        $subIds = $user->subordinates()->pluck('id')->toArray();
        $allowed = array_merge([$user->id], $subIds);
        $query->where(function ($q) use ($allowed, $user) {
            $q->whereIn('created_by', $allowed)
                ->orWhereHas('donatur', fn ($q2) => $q2->where('pic', $user->id));
        });
        $query->whereBetween('tanggal_transaksi', ['2025-12-12', '2025-12-12']);
        // cleanup debug file if present
file_exists(base_path('tmp/transaksi_debug.sql')) && @unlink(base_path('tmp/transaksi_debug.sql'));


        $response = $this->actingAs($user)->getJson('/admin/api/transaksi?tanggal_from=2025-12-12&tanggal_to=2025-12-12');
        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('2025-12-12', $data[0]['tanggal_transaksi']);
    }

    public function test_tanggal_query_with_range_syntax_returns_matching_records()
    {
        $user = User::factory()->create();

        // create 3 records within range and 1 outside
        Transaksi::create(['nominal' => 11, 'tanggal_transaksi' => '2025-12-01', 'created_by' => $user->id]);
        Transaksi::create(['nominal' => 12, 'tanggal_transaksi' => '2025-12-05', 'created_by' => $user->id]);
        Transaksi::create(['nominal' => 13, 'tanggal_transaksi' => '2025-12-15', 'created_by' => $user->id]);
        Transaksi::create(['nominal' => 14, 'tanggal_transaksi' => '2025-12-20', 'created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/admin/api/transaksi?tanggal=2025-12-01+to+2025-12-15');
        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        // 3 records in the range
        $this->assertCount(3, $data);
    }
}
