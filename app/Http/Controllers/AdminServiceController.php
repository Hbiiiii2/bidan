<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Services\ServiceScheduleSynchronizer;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('midwives')->orderByDesc('created_at')->get();

        return view('pages.admin.services.index', compact('services'));
    }

    public function create()
    {
        $midwives = User::role('midwife')->orderBy('name')->get();

        return view('pages.admin.services.create', compact('midwives'));
    }

    public function store(Request $request, ServiceScheduleSynchronizer $scheduleSynchronizer)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'tag'                  => 'required|string|max:80',
            'description'          => 'nullable|string',
            'available_from_date'  => 'required|date',
            'available_until_date' => 'required|date|after_or_equal:available_from_date',
            'available_start_time' => 'required|date_format:H:i',
            'available_end_time'   => 'required|date_format:H:i|after:available_start_time',
            'price'                => 'required|numeric|min:0',
            'midwife_ids'          => 'nullable|array',
            'midwife_ids.*'        => 'exists:users,id',
            'midwife_quotas'       => 'nullable|array',
            'midwife_quotas.*'     => 'nullable|integer|min:1|max:200',
        ]);

        $validated['available_date'] = $validated['available_from_date'];
        $validated['type'] = $this->resolveType($validated['tag']);

        $midwife_ids = $validated['midwife_ids'] ?? [];
        $midwifeQuotas = $validated['midwife_quotas'] ?? [];
        unset($validated['midwife_ids']);
        unset($validated['midwife_quotas']);

        $service = Service::create($validated);

        $service->midwives()->sync($this->buildMidwifeSyncPayload($midwife_ids, $midwifeQuotas));
        $scheduleSynchronizer->sync($service);

        return redirect('/admin/services')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        $midwives = User::role('midwife')->orderBy('name')->get();

        return view('pages.admin.services.edit', compact('service', 'midwives'));
    }

    public function update(Request $request, Service $service, ServiceScheduleSynchronizer $scheduleSynchronizer)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'tag'                  => 'required|string|max:80',
            'description'          => 'nullable|string',
            'available_from_date'  => 'required|date',
            'available_until_date' => 'required|date|after_or_equal:available_from_date',
            'available_start_time' => 'required|date_format:H:i',
            'available_end_time'   => 'required|date_format:H:i|after:available_start_time',
            'price'                => 'required|numeric|min:0',
            'midwife_ids'          => 'nullable|array',
            'midwife_ids.*'        => 'exists:users,id',
            'midwife_quotas'       => 'nullable|array',
            'midwife_quotas.*'     => 'nullable|integer|min:1|max:200',
        ]);

        $validated['available_date'] = $validated['available_from_date'];
        $validated['type'] = $this->resolveType($validated['tag']);

        $midwife_ids = $validated['midwife_ids'] ?? [];
        $midwifeQuotas = $validated['midwife_quotas'] ?? [];
        unset($validated['midwife_ids']);
        unset($validated['midwife_quotas']);

        $service->update($validated);
        $service->midwives()->sync($this->buildMidwifeSyncPayload($midwife_ids, $midwifeQuotas));
        $scheduleSynchronizer->sync($service);

        return redirect('/admin/services')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect('/admin/services')->with('success', 'Layanan berhasil dihapus.');
    }

    private function resolveType(string $tag): string
    {
        return str_contains(strtolower($tag), 'imun') ? 'immunization' : 'consultation';
    }

    private function buildMidwifeSyncPayload(array $midwifeIds, array $midwifeQuotas): array
    {
        $payload = [];

        foreach ($midwifeIds as $midwifeId) {
            $quota = (int) ($midwifeQuotas[$midwifeId] ?? ServiceScheduleSynchronizer::DEFAULT_MIDWIFE_DAILY_QUOTA);

            $payload[$midwifeId] = [
                'max_daily_quota' => max(1, $quota),
            ];
        }

        return $payload;
    }
}
