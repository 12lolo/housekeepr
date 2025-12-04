<?php

namespace App\Services;

use Algolia\AlgoliaSearch\Api\SearchClient;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AlgoliaService
{
    protected $client;

    public function __construct()
    {
        $this->client = SearchClient::create(
            config('services.algolia.app_id'),
            config('services.algolia.admin_key')
        );
    }

    /**
     * Index all owners in Algolia
     */
    public function indexOwners()
    {
        $owners = User::where('role', 'owner')
            ->withCount('hotels')
            ->get();

        $records = $owners->map(function ($owner) {
            return [
                'objectID' => 'owner_' . $owner->id,
                'type' => 'Eigenaar',
                'title' => $owner->name,
                'name' => $owner->name,
                'email' => $owner->email,
                'description' => $owner->email . ' - ' . ($owner->hotels_count ?? 0) . ' hotel(s)',
                'section' => 'eigenaren',
                'action' => 'view-owner-btn-' . $owner->id,
                'status' => $owner->status,
                'created_at' => $owner->created_at->timestamp,
            ];
        })->toArray();

        try {
            $this->client->saveObjects('housekeepr_admin', $records);
            Log::info('Algolia: Indexed ' . count($records) . ' owners');
            return true;
        } catch (\Exception $e) {
            Log::error('Algolia indexing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Index admin actions in Algolia
     */
    public function indexActions()
    {
        $actions = [
            [
                'objectID' => 'action_add_owner',
                'type' => 'Actie',
                'title' => 'Nieuwe Eigenaar Toevoegen',
                'description' => 'Voeg een nieuwe eigenaar toe aan het systeem',
                'section' => 'eigenaren',
                'action' => 'openAddOwnerBtn',
            ],
            [
                'objectID' => 'action_view_dashboard',
                'type' => 'Navigatie',
                'title' => 'Dashboard',
                'description' => 'Ga naar het admin dashboard',
                'section' => 'dashboard',
            ],
            [
                'objectID' => 'action_view_owners',
                'type' => 'Navigatie',
                'title' => 'Eigenaren Overzicht',
                'description' => 'Bekijk alle eigenaren',
                'section' => 'eigenaren',
            ],
            [
                'objectID' => 'action_view_audit_log',
                'type' => 'Navigatie',
                'title' => 'Audit Log',
                'description' => 'Bekijk de audit log en recente activiteit',
                'section' => 'audit-log',
            ],
        ];

        try {
            $this->client->saveObjects('housekeepr_admin', $actions);
            Log::info('Algolia: Indexed ' . count($actions) . ' actions');
            return true;
        } catch (\Exception $e) {
            Log::error('Algolia indexing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Index a single owner
     */
    public function indexOwner(User $owner)
    {
        $record = [
            'objectID' => 'owner_' . $owner->id,
            'type' => 'Eigenaar',
            'title' => $owner->name,
            'name' => $owner->name,
            'email' => $owner->email,
            'description' => $owner->email . ' - ' . ($owner->hotels()->count()) . ' hotel(s)',
            'section' => 'eigenaren',
            'action' => 'view-owner-btn-' . $owner->id,
            'status' => $owner->status,
            'created_at' => $owner->created_at->timestamp,
        ];

        try {
            $this->client->saveObject('housekeepr_admin', $record);
            return true;
        } catch (\Exception $e) {
            Log::error('Algolia indexing failed for owner ' . $owner->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an owner from the index
     */
    public function deleteOwner($ownerId)
    {
        try {
            $this->client->deleteObject('housekeepr_admin', 'owner_' . $ownerId);
            return true;
        } catch (\Exception $e) {
            Log::error('Algolia deletion failed for owner ' . $ownerId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Index all data (owners + actions)
     */
    public function indexAll()
    {
        $this->indexOwners();
        $this->indexActions();
    }

    /**
     * Clear the entire index
     */
    public function clearIndex()
    {
        try {
            $this->client->clearObjects('housekeepr_admin');
            Log::info('Algolia: Index cleared');
            return true;
        } catch (\Exception $e) {
            Log::error('Algolia clear index failed: ' . $e->getMessage());
            return false;
        }
    }
}

