<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LibroGuardarRequest extends FormRequest
{
  public function authorize()
  {
    return true; // Permite a cualquier usuario usar este request
  }

  public function rules()
  {
    return [
      'codigoBarras' => 'nullable|string|max:255',
      'autorId' => 'required|exists:autores,id',
      'editorialId' => 'required|exists:editoriales,id',
      'areaId' => 'required|exists:areas,id',
      'clave' => 'nullable|string|max:255',
      // 'isbn' => 'nullable|string|max:255',
      'titulo' => 'required|string|max:50',
      'anio' => 'required|digits:4',
      'status' => 'nullable|string',
      'archivoPDF' => 'required|file|mimes:pdf|max:10240',
    ];
  }

  public function messages()
  {
    return [
      'titulo.required' => 'El título es obligatorio.',
      'titulo.max' => 'El título no puede exceder los 50 caracteres.',
      'anio.required' => 'El año es obligatorio.',
      'anio.digits' => 'El año debe tener 4 dígitos.',
      'autorId.required' => 'Debes seleccionar un autor.',
      'autorId.exists' => 'El autor seleccionado no es válido.',
      'editorialId.required' => 'Debes seleccionar una editorial.',
      'editorialId.exists' => 'La editorial seleccionada no es válida.',
      'areaId.required' => 'Debes seleccionar un área.',
      'areaId.exists' => 'El área seleccionada no es válida.',
      'archivoPDF.required' => 'El archivo PDF es obligatorio.',
      'archivoPDF.file' => 'Debes subir un archivo válido.',
      'archivoPDF.mimes' => 'El archivo debe ser un PDF.',
      'archivoPDF.max' => 'El archivo no debe pesar más de 10MB.',
    ];
  }
}
