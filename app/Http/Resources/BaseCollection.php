<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => [
                'page' => $this->currentPage(),
                'limit' => $this->perPage(),
                'total' => $this->total(),
                'totalPages' => $this->lastPage(),
            ],
        ];
    }

    public function withResponse($request, $response)
    {
        $data = json_decode($response->getContent(), true);
        unset($data['links'], $data['meta']);
        $response->setContent(json_encode($data));
    }
}
