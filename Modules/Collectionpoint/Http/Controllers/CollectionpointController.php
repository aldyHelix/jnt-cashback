<?php

namespace Modules\Collectionpoint\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Collectionpoint\Datatables\CollectionpointDatatables;
use Modules\Collectionpoint\Http\Requests\CollectionpointRequest;
use Modules\Collectionpoint\Models\Collectionpoint;

class CollectionpointController extends Controller
{
    //
    public function index()
    {
        ladmin()->allows(['ladmin.collectionpoint.index']);

        if(request()->has('datatables')) {
            return CollectionpointDatatables::renderData();
        }

        return view('collectionpoint::index');
    }

    public function create()
    {
        ladmin()->allows(['ladmin.collectionpoint.create']);
        $data['data'] = new Collectionpoint();

        return view('collectionpoint::create', $data);
    }

    public function edit($id)
    {
        ladmin()->allows(['ladmin.collectionpoint.update']);
        $data['data'] = Collectionpoint::findOrFail($id);

        return view('collectionpoint::edit', $data);
    }

    public function store(CollectionpointRequest $request)
    {
        ladmin()->allows(['ladmin.collectionpoint.create']);

        return $request->createCollectionpoint();
    }

    public function update(CollectionpointRequest $request, $id)
    {
        ladmin()->allows(['ladmin.collectionpoint.update']);

        return $request->updateCollectionpoint(
            Collectionpoint::findOrFail($id)
        );
    }

    public function destroy($id)
    {
        ladmin()->allows(['ladmin.collectionpoint.delete']);

        $cp = Collectionpoint::findOrFail($id);

        if ($cp->delete()) {
            toastr()->success('Data Collection point has been deleted successfully!', 'Congrats');
        } else {
            toastr()->error('The collection point cannot be deleted, because it is still used by some users!', 'Opps!');
        }

        return redirect()->back();
    }
}
