<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeliculaRequest;
use App\Http\Requests\UpdatePeliculaRequest;
use App\Models\Pelicula;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use OutOfRangeException;

class PeliculaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('peliculas.index', [
            'peliculas' => Pelicula::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('peliculas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeliculaRequest $request)
    {
        $validate = $request->validated();

        Pelicula::create($validate);

        return redirect()->route('peliculas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelicula $pelicula)
    {

        $peliculas = Pelicula::with('proyecciones.entradas')->where('id', $pelicula->id)->get();

        $entradas = 0;
        foreach ($peliculas as $pelicula) {
            foreach ($pelicula->proyecciones as $proyeccion) {
                $entradas += $proyeccion->entradas->count();
            }
        }
        return view('peliculas.show', [
            'pelicula' => $pelicula,
            'entradas' => $entradas,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelicula $pelicula)
    {

        $peliculas = Pelicula::with('proyecciones.entradas')->where('id', $pelicula->id)->get();
        $entradas = '';
        foreach ($peliculas as $pelicula) {
            foreach ($pelicula->proyecciones as $proyeccion) {

                $entradas = $proyeccion->entradas;
            }
        }

        $existe = false;

        if ($entradas !== '') {
            $existe = true;
        }
        return view('peliculas.edit', [
            'pelicula' => $pelicula,
            'existe' => $existe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeliculaRequest $request, Pelicula $pelicula)
    {
        $validate = $request->validated();

        $pelicula->fill($validate);

        $pelicula->save();

        return redirect()->route('peliculas.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelicula $pelicula)
    {

        $peliculas = Pelicula::with('proyecciones.entradas')->where('id', $pelicula->id)->get();
        $entradas = '';
        foreach ($peliculas as $pelicula) {
            foreach ($pelicula->proyecciones as $proyeccion) {

                $entradas = $proyeccion->entradas;
            }
        }

        $existe = false;

        if ($entradas !== '') {
            $existe = true;
        }

        if ($existe === false) {

            $pelicula->delete();
        }

        return redirect()->route('peliculas.index');
    }
}
