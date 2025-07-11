<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query();

        // Filtros
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'Todas') {
            $query->where('category', $request->category);
        }

        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('created_at', $date);
        }

        if ($request->filled('chapter') && $request->chapter !== 'Todos los capítulos') {
            $query->where('chapter', $request->chapter);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Obtener categorías y capítulos únicos para los filtros
        $categories = Document::distinct('category')->pluck('category');
        $chapters = Document::distinct('chapter')->whereNotNull('chapter')->pluck('chapter');

        return view('documents.index', compact('documents', 'categories', 'chapters'));
    }

    public function create()
    {
        $categories = ['Administrativo', 'Operativo', 'Capacitación', 'Reportes'];
        $chapters = ['Manual de inducción', 'Formularios', 'Matrices de seguimiento', 'Reportes anuales'];
        
        return view('documents.create', compact('categories', 'chapters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'chapter' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getClientOriginalExtension();
        }

        $validated['uploaded_by'] = auth()->id();
        
        Document::create($validated);

        return redirect()->route('documents.index')
                         ->with('success', 'Documento subido exitosamente.');
    }

    public function show(Document $document)
    {
        return response()->json([
            'id' => $document->id,
            'title' => $document->title,
            'description' => $document->description,
            'category' => $document->category,
            'chapter' => $document->chapter,
            'file_name' => $document->file_name,
            'file_size' => $this->formatFileSize($document->file_size),
            'file_type' => strtoupper($document->file_type),
            'uploaded_by' => $document->uploader ? $document->uploader->name : 'Sistema',
            'created_at' => $document->created_at->format('d/m/Y'),
            'updated_at' => $document->updated_at->format('d/m/Y H:i'),
        ]);
    }

    public function download(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        return Response::download($filePath, $document->file_name);
    }

    public function destroy(Document $document)
    {
        // Eliminar archivo físico
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
                         ->with('success', 'Documento eliminado exitosamente.');
    }

    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}