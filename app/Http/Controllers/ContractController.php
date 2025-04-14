<?php

namespace App\Http\Controllers;

use App\Services\WeclappService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContractController extends Controller
{

    public function __construct(protected WeclappService $weclappService)
    {
    }

    /**
     * Zeigt den Vertrag an
     *
     * @return View
     */
    public function show(): View
    {
        $entityId = '556515';

        try {
            $contract = $this->weclappService->getContractById($entityId);
            $rateLimit = $this->weclappService->getCurrentRateLimit();

            return view('contracts.show', [
                'contract' => $contract,
                'rateLimit' => $rateLimit
            ]);
        } catch (\Exception $e) {
            return view('contracts.show', [
                'error' => $e->getMessage(),
                'rateLimit' => $this->weclappService->getCurrentRateLimit()
            ]);
        }
    }

    /**
     * Aktualisiert den Vertrag
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $entityId = '556515';

        try {
            $contract = $this->weclappService->getContractById($entityId);
            $contract['description'] = $request->input('description');

            $this->weclappService->updateContract($entityId, $contract);

            return redirect()->route('contracts.show')->with('success', 'Vertrag wurde erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            return redirect()->route('contracts.show')->with('error', 'Fehler beim Aktualisieren des Vertrags: ' . $e->getMessage());
        }
    }

    /**
     * Aktualisiert das Rate-Limiting
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateRateLimit(Request $request): RedirectResponse
    {
        $request->validate([
            'rate_limit' => 'required|integer|min:1|max:100',
        ]);

        $this->weclappService->updateRateLimit($request->input('rate_limit'));

        return redirect()->route('contracts.show')->with('success', 'Rate-Limit wurde erfolgreich aktualisiert.');
    }
}
