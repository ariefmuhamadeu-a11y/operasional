<?php
// app/Http/Requests/OrderImportRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderImportRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
    return ['file' => ['required', 'file', 'mimes:xlsx,xls']]; // boleh tambah 'csv' jika perlu
}
}
