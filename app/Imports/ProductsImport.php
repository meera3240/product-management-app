<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if (is_null($row['name']) || is_null($row['code']) || is_null($row['price']) || is_null($row['description']) || is_null($row['status'])) {
                continue; // Skip rows with null values
            }
            $validator = Validator::make($row->toArray(), [
                'name' => 'required|string',
                'code' => 'required|string|unique:products,code',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'status' => 'required|string',
            ]);

            if ($validator->fails()) {
                continue; // Skip invalid rows
            }

            Product::create([
                'user_id' => Auth::id(),
                'name' => $row['name'],
                'code' => strtoupper($row['code']),
                'description' => $row['description'],
                'price' => $row['price'],
                'status' => $row['status'],
            ]);
        }
    }
}

