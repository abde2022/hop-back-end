<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organisation\EditOrganisationRequest;
use App\Http\Requests\Organisation\IsAlreadyExistRequest;
use App\Http\Requests\Organisation\NewOrganisationRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Traits\GeneraleTrait;
use App\Models\Organisation;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrganisationController extends Controller
{
    use GeneraleTrait;

    /**
     * Display a listing of the organisation.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function index(PaginationRequest $request)
    {
        try {
            // initile-pagination
            $itemPerPage = 5;
            $currentPage = 1;

            // validation
            $validatedData = $request->validated();

            // specified retun msg
            $returnMsg = "Organisations Selected Successfully !";

            // initile-pagination
            $itemPerPage = 5;
            $currentPage = 1;

            $organisations = Organisation::query()->orderBy('id', 'DESC')->get();;
            $totalOrganisation = $organisations->count();

            // pagination
            if ($request->has('itemPerPage') && $request->itemPerPage  != "") {
                $itemPerPage = $validatedData['itemPerPage'];
            }
            if ($request->has('currentPage') && $request->itemPerPage  != "") {
                $currentPage = $validatedData['currentPage'];
            }


            // empty-records
            if ($totalOrganisation == 0) {
                $returnMsg = "No Organisations Selected !";
            }

            $pagedItems = $organisations->slice(($currentPage - 1) * $itemPerPage, $itemPerPage)->all();
            $paginatedItems = new LengthAwarePaginator($pagedItems, $totalOrganisation, $itemPerPage, $currentPage);
            $data =  $paginatedItems->withPath(request()->url());


            return  $this->returnData("data", $data, 200, $returnMsg);
        } catch (ClientException $e) {
            return $this->returnError(500, "Something Went Wrong");
        }
    }
    /**
     * Display the specified resource.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // specified retun msg
            $returnMsg = "Organisation With $id Selected Successfully !";

            $organisationId = Organisation::find($id);

            // empty-records
            if (empty($organisationId)) {
                $returnMsg = "Organisation With $id Not Found !";
                $organisationId = [];
            }
            return  $this->returnData("data", $organisationId, 200, $returnMsg);
        } catch (ClientException $e) {
            return $this->returnError(500, "Something Went Wrong");
        }
    }
    /**
     * create new organisation
     * 
     * @param NewOrganisationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function store(NewOrganisationRequest $request)
    {
        try {
            // validation
            $validatedData  = $request->validated();

            // create new organisation
            $newOrganisation                = new Organisation();
            $newOrganisation->cle           = $validatedData["cle"];
            $newOrganisation->nom           = $validatedData["nom"];
            $newOrganisation->adresse       = $validatedData["adresse"];
            $newOrganisation->code_postal   = $validatedData["code_postal"];
            $newOrganisation->ville         = $validatedData["ville"];
            $newOrganisation->statut        = $validatedData["statut"];
            $newOrganisation->created_at    = $validatedData["created_at"];
            $newOrganisation->updated_at    = $validatedData["updated_at"];

            // check is created
            if (!$newOrganisation->save()) {
                return $this->returnSuccessMessage(500, "Something Went Wrong");
            }

            return [$this->returnSuccessMessage(201, "Organisation Created Successfully")->original, $newOrganisation->id];
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
    /**
     * Update the specified resource in storage.
     * 
     * @param int $id
     * @param EditOrganisationRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function update($id, EditOrganisationRequest $request)
    {
        try {
            // validation
            $validatedData  = $request->validated();

            // check
            $oldOrganisation                = Organisation::find($id);
            if (empty($oldOrganisation)) {
                return $this->returnError(500, "No Organisation With $id Found To Edit !");
            }

            // edit organisation
            $oldOrganisation->cle           = $validatedData["cle"];
            $oldOrganisation->nom           = $validatedData["nom"];
            $oldOrganisation->adresse       = $validatedData["adresse"];
            $oldOrganisation->code_postal   = $validatedData["code_postal"];
            $oldOrganisation->ville         = $validatedData["ville"];
            $oldOrganisation->statut        = $validatedData["statut"];
            $oldOrganisation->updated_at    = $validatedData["updated_at"];

            // check is edited
            if (!$oldOrganisation->save()) {
                return $this->returnSuccessMessage(500, "Something Went Wrong");
            }

            return $this->returnSuccessMessage(200, "Organisation Edited Successfully");
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function destroy($id)
    {
        try {
            // check-organisation-existence
            $organisation = Organisation::find($id);
            if (empty($organisation)) {
                return $this->returnError(200, "No Organisation To Delete");
            }

            // delete-organisation
            if (!$organisation->delete()) {
                return $this->returnError(500, "Something Went Wrong");
            }

            return $this->returnSuccessMessage(200, "Organisation $id Deleted Successfully");
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
    /**
     * is already exist record with same name
     * 
     * @param IsAlreadyExistRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function isAlreadyExist(IsAlreadyExistRequest $request)
    {
        try {

            // initiale existenec of record
            $isExist       = false;

            // validation
            $validatedData          = $request->validated();
            $organisationName       = $validatedData["nom"];

            // check-organisation-existence
            $organisation = DB::table('organisation')->where('nom', 'like',  "$organisationName%")->get();

            if ($organisation->count() > 0) {
                $isExist       = true;
            }

            return $this->returnData("data", $isExist,  200, "is Organisation Already Exist With The Same Name");
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
}
