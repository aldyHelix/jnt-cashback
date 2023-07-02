<?php

namespace Modules\CollectionPoint\Http\Controllers;

use Illuminate\Http\Request;
use Modules\CollectionPoint\Datatables\CollectionPointDatatables;
use Modules\CollectionPoint\Http\Requests\CollectionPointRequest;
use Modules\CollectionPoint\Models\CollectionPoint;

class CollectionPointController extends Controller
{
    //
    public function index() {
        ladmin()->allows(['ladmin.collectionpoint.index']);

        if( request()->has('datatables') ) {
            return CollectionPointDatatables::renderData();
        }

        return view('collectionpoint::index');
    }

    public function create() {
        ladmin()->allows(['ladmin.collectionpoint.create']);
        $data['data'] = new CollectionPoint;

        return view('collectionpoint::create', $data);
    }

    public function edit($id) {
        ladmin()->allows(['ladmin.collectionpoint.update']);
        $data['data'] = CollectionPoint::findOrFail($id);

        return view('collectionpoint::edit', $data);
    }

    public function store(CollectionPointRequest $request) {
        ladmin()->allows(['ladmin.collectionpoint.create']);

        return $request->createCollectionPoint();
    }

    public function update(CollectionPointRequest $request, $id) {
        ladmin()->allows(['ladmin.collectionpoint.update']);

        return $request->updateCollectionPoint(
            CollectionPoint::findOrFail($id)
        );
    }

    public function destroy($id) {
        ladmin()->allows(['ladmin.collectionpoint.delete']);

        $cp = CollectionPoint::findOrFail($id);

        if ($cp->delete()) {
            session()->flash('success', 'Collection point has been deleted!');
        } else {
            session()->flash('danger', 'The collection point cannot be deleted, because it is still used by some users!');
        }

        return redirect()->back();
    }
}
