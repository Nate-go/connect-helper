<?php

namespace App\Services\BusinessServices;

class FormResponseService {

    public function connectionFormResponse($connection) {
        if(!$connection) {
            return [];
        }

        $data = [
            'id' => $connection->id,
            'name' => $connection->name,
            'note' => $connection->note,
            'type' => $connection->type,
            'status' => $connection->status,
            'owner' => $connection->user?->name,
            'created_at' => $connection->created_at,
            'tags' => $connection->tags->pluck('name')->toArray(),
            'users' => $connection->users->pluck('name')->toArray(),
        ];

        return $data;
    }

    public function connectionsFormResponse($connections) {
        if(!$connections) {
            return [];
        }

        $newConnections = [];

        foreach ($connections as $connection) {
            $newConnections[] = $this->connectionFormResponse($connection);
        }

        return $newConnections;
    }

}