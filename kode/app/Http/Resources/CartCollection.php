<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'data' => $this->collection,
        ];

        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data['pagination'] = [
                'total'         => $this->total(),
                'per_page'      => $this->perPage(),
                'current_page'  => $this->currentPage(),
                'last_page'     => $this->lastPage(),
                'from'          => $this->firstItem(),
                'to'            => $this->lastItem(),
                'prev_page_url' => $this->previousPageUrl(),
                'next_page_url' => $this->nextPageUrl(),
                'path'          => $this->path(),
                'query'         => app('request')->query(),
            ];
        }

        return $data;
    }
}
