<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\EditContactRequest;
use App\Http\Requests\Contact\IsAlreadyExistRequest;
use App\Http\Requests\Contact\NewContactRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Traits\GeneraleTrait;
use App\Models\Contact;
use App\Models\Organisation;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    use GeneraleTrait;

    /**
     * Display a listing of the contact.
     * 
     * @param PaginationRequest $request
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
            $returnMsg = "Contacts Selected Successfully !";

            $contacts = Contact::query()->orderBy('id', 'DESC')->get();;
            $totalcontacts = $contacts->count();

            // pagination
            if ($request->has('itemPerPage') && $request->itemPerPage  != "") {
                $itemPerPage = $validatedData['itemPerPage'];
            }
            if ($request->has('currentPage') && $request->itemPerPage  != "") {
                $currentPage = $validatedData['currentPage'];
            }


            // empty-records                   
            if ($totalcontacts == 0) {
                $returnMsg = "No Contacts Selected !";
            }

            $pagedItems = $contacts->slice(($currentPage - 1) * $itemPerPage, $itemPerPage)->all();
            $paginatedItems = new LengthAwarePaginator($pagedItems, $totalcontacts, $itemPerPage, $currentPage);
            $data =  $paginatedItems->withPath(request()->url());

            return  $this->returnData("data", $data, 200, $returnMsg);
        } catch (ClientException $e) {
            return $this->returnError(500, "Something Went Wrong");
        }
    }
    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function show($id)
    {
        try {
            // specified retun msg
            $returnMsg = "Contact With $id Selected Successfully !";

            $contactId = Contact::find($id);
            // empty-records
            if (empty($contactId)) {
                $returnMsg = "Contact With $id Not Found !";
                $contactId = [];
            }
            return  $this->returnData("data", $contactId, 200, $returnMsg);
        } catch (ClientException $e) {
            return $this->returnError(500, "Something Went Wrong");
        }
    }

    /**
     * store new contact
     * 
     * @param NewContactRequest $$request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function store(NewContactRequest $request)
    {
        try {
            // validation
            $validatedData = $request->validated();

            // check-organisation_id-existence
            $organisation = Organisation::find($validatedData['organisation_id']);
            if (!$organisation) {
                return $this->returnError(500, "No Organisation To Affect Contact");
            }

            // new conatct
            $newContact                     = new Contact();
            $newContact->cle                = $validatedData['cle'];
            $newContact->organisation_id    = $validatedData['organisation_id'];
            $newContact->e_mail             = $validatedData['e_mail'];
            $newContact->nom                = $validatedData['nom'];
            $newContact->prenom             = $validatedData['prenom'];
            $newContact->telephone_fixe     = $validatedData['telephone_fixe'];
            $newContact->service            = $validatedData['service'];
            $newContact->fonction           = $validatedData['fonction'];
            $newContact->created_at         = $validatedData['created_at'];
            $newContact->updated_at         = $validatedData['updated_at'];

            // check-is-created
            if (!$newContact->save()) {
                return $this->returnSuccessMessage(500, "Something Went Wrong");
            }

            return $this->returnSuccessMessage(201, "Contact Created Successfully");
        } catch (ClientException $ce) {
            return $this->returnError(500, "Something Went Wrong");
        }
    }
    /**
     * Update the specified resource in storage.
     * 
     * @param int $id
     * @param EditContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function update($id, EditContactRequest $request)
    {
        try {
            // validation
            $validatedData  = $request->validated();

            // check-contact-existence
            $oldContact                = Contact::find($id);
            if (empty($oldContact)) {
                return $this->returnError(500, "No Contact With $id Found To Edit !");
            }

            // edit contact            
            $oldContact->cle                = $validatedData['cle'];
            $oldContact->e_mail             = $validatedData['e_mail'];
            $oldContact->nom                = $validatedData['nom'];
            $oldContact->prenom             = $validatedData['prenom'];
            $oldContact->telephone_fixe     = $validatedData['telephone_fixe'];
            $oldContact->service            = $validatedData['service'];
            $oldContact->fonction           = $validatedData['fonction'];
            $oldContact->updated_at         = $validatedData['updated_at'];

            // check is edited
            if (!$oldContact->save()) {
                return $this->returnSuccessMessage(500, "Something Went Wrong");
            }

            return $this->returnSuccessMessage(200, "Contact Edited Successfully");
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
            // check-contact-existence
            $contact = Contact::find($id);
            if (empty($contact)) {
                return $this->returnError(200, "No Contact To Delete");
            }

            // delete-Contact
            if (!$contact->delete()) {
                return $this->returnError(500, "Something Went Wrong");
            }

            return $this->returnSuccessMessage(200, "Contact $id Deleted Successfully");
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
            $contactFirstName  = $validatedData["nom"];
            $contactLastName   = $validatedData["prenom"];

            // check-contact-existence
            $contact = DB::table('contact')
                ->where('nom', 'like',  "$contactFirstName%")
                ->where('prenom', 'like',  "$contactLastName%")
                ->get();

            if ($contact->count() > 0) {
                $isExist       = true;
            }

            return $this->returnData("data", $isExist,  200, "is Contact Already Exist With The Same FirstName and LastName");
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
    /**
     * get contact organisation
     * @param PaginationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contactWithOrganisation(PaginationRequest $request)
    {
        try {
            // specified retun msg
            $returnMsg = "Contact With Organisation Selected Successfully !";

            // initile-pagination
            $itemPerPage = 5;
            $currentPage = 1;

            // validation
            $validatedData = $request->validated();

            $contactsOrganisation = Contact::with('organisation')->get();
            $totalcontacts = $contactsOrganisation->count();

            // pagination
            if ($request->has('itemPerPage') && $request->itemPerPage  != "") {
                $itemPerPage = $validatedData['itemPerPage'];
            }
            if ($request->has('currentPage') && $request->itemPerPage  != "") {
                $currentPage = $validatedData['currentPage'];
            }

            // empty-records                   
            if ($totalcontacts == 0) {
                $returnMsg = "No Relation Contact-Organisation Found !";
            }

            $pagedItems = $contactsOrganisation->slice(($currentPage - 1) * $itemPerPage, $itemPerPage)->all();
            $paginatedItems = new LengthAwarePaginator($pagedItems, $totalcontacts, $itemPerPage, $currentPage);
            $data =  $paginatedItems->withPath(request()->url());

            return $this->returnData("data", $data,  200, $returnMsg);
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
    /**
     * get contact organisation
     * @param int $contactId
     * @return \Illuminate\Http\JsonResponse
     */
    public function contactIDWithOrganisation($contactId)
    {
        try {
            // specified retun msg
            $returnMsg = "Contact With $contactId Has Organisation  !";

            $contactIdWithOrganisation  = Contact::with('organisation')
                ->where('id', '=', $contactId)
                ->get();

            // empty-records
            if ($contactIdWithOrganisation->count() == 0) {
                $returnMsg = "Contact With $contactId Not Found !";
                $contactIdWithOrganisation = [];
            }
            return  $this->returnData("data", $contactIdWithOrganisation, 200, $returnMsg);
        } catch (ClientException $ce) {
            return $this->returnSuccessMessage(500, "Something Went Wrong");
        }
    }
}
