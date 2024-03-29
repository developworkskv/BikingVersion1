<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use App\Repositories\ClientServiceRepository;

class ClientController extends BaseController
{
    //VALIDACION CLIENTS
    public function dataclientById($id_client)
    {
        $client = DB::table('bd_clients')
            ->where('bd_clients_id', $id_client)
            ->first();

        $arrayDataClient = array();
        if (empty($client)) {
            return $this->sendError('Cliente no encontrado ó credenciales incorrectas.', $client);
        }
        array_push($arrayDataClient, $client);
        return $arrayDataClient;
    }
    public function dataclientByPassport($passport_client)
    {
        // verificar si existe el usuario
        $client = DB::table('bd_clients')
        ->join('bd_persons', 'bd_clients.bd_persons_id', '=', 'bd_persons.bd_persons_id')
        ->where('bd_persons.email', $passport_client)
        ->first();
        
        // get ==> Array
        //first ==> Objeto
        $arrayDataClient = array(); // almacen
        if (empty($client)) {
            return $this->sendError('Cliente no encontrado ó credenciales incorrectas.', $client);
        }
        array_push($arrayDataClient, $client);
        return $arrayDataClient;
    }
 
    //GET - READ ALL CLIENT 
    public function getAllClient($id_org)
    {
        $gestionClient = new ClientServiceRepository;

        $clients = $gestionClient->getAllClients($id_org);
        if (count($clients) == 0) {
            // no hay Datos
            return $this->sendError('No existen registros', 'Ningun registro insertado en esta tabla.');

        } else {
            return $this->sendResponse($clients, 'Usuario creado correctamente.');

        }

    }
    // GET - CLIENT BY ID
    public function getClientById($id_org)
    {
        $gestionClient = new ClientServiceRepository;
        $client = $gestionClient->getClientsById($id_org);

        if (count($client) == 0) {
            // no hay Datos
            return $this->sendError('No existen registros', 'Ningun registro insertado en esta tabla.');
        } else {
            return $this->sendResponse($client, 'Usuario administrador creado correctamente.');
        }

    }
     //POST - CREAR CLIENTE
     public function createClient(Request $request, $id_org)
     {
 
         $gestionClient = new ClientServiceRepository;
         //validate if existe all Request // return count($request->all()); == 6
         if (count($request->all()) < 4) {
             return $this->sendError('Datos Incompletos', 'Error, porfavor ingresa los datos solicitados');
         }
 
         if (count($request->all()) >= 7 ) {
        
             // verificar si ese -- ya existe registrado
             $clientExist = $gestionClient->clientExist($request->input('passport'));
             // si existe no existe usuario registrado con ese email inserte
             if (count($clientExist) == 0) {
        
                 $insert = $gestionClient->newClient(
                     //CAMPOS PERSONA
                     $request->input('nameP'),
                     $request->input('lastNameP'),
                     $request->input('emailP'),
                     $request->input('fechaNacimientoP'),
                     $request->input('genderP'),
                     $request->input('dniP'),
                     $request->input('descriptionPersonP'),

                    //CAMPOS DE CLIENT
                     $request->input('nacionality'),
                     $request->input('height'),
                     $request->input('weight'),
                     $request->input('passport'),
                     $request->input('description1'),
                     $id_org
                 );
                 return $this->sendResponse('Correcto Cliente creado', 'Usuario administrador creado correctamente.');
             } else {
                 // Existe un registro en el Array $userExist - un registro en la base con el email ingresado
                 return $this->sendError('Cliente ya registrado', 'Error, ya existe un Cliente con el mismo Passport.');
             }
         }
     }


     //UPDATE - MODIFI DATA CLIENTS
    public function updateClient($id_person, Request $request)
    {
        $gestionClient = new ClientServiceRepository;
        // verficate if exist this user
        $clientIsRegister = $gestionClient->getClientsById($id_person);
        if (count($clientIsRegister) == 0) {
            // no hay Datos
            return $this->sendError('Cliente no encontrado', 'Ningun registro insertado en esta tabla.');
        } else {
            if (count($request->all()) >= 6) {
                //EDIT TABLE bd_persons
                $gestionClient->updateClientsPerson(
                    $id_person,
                    $request->input('nameP'),
                    $request->input('lastNameP'),
                    $request->input('emailP'),
                    $request->input('fechaNacimientoP'),
                    $request->input('genderP'),
                    $request->input('dniP'),
                    $request->input('descriptionPersonP'),
                    $request->input('nacionalityC'),
                    $request->input('heightC'),
                    $request->input('weightC'),
                    $request->input('passportC'),
                    $request->input('description1C'),
                    $request->input('description2C')
                );
                //getIdPerson modificated
                $clientEdit = $gestionClient->getClientsById($id_person);
                // EDIT TABLE bd_users where idPersons
                $update = $gestionClient->updateClient($clientEdit[0]->bd_clients_id,
                    $request->input('bd_persons_id')
                                 ); // SI SE QUIERE MODIFICAR ESTE CAMPO CREAR OTRA FUNCION - CAMBIAR datos
                return $this->sendResponse($userIsRegister, 'Cliente modificado correctamente.');

            } else {
                return $this->sendError('Ingresar los datos solicitados', 'Ningun registro modificado en esta tabla.');

            }
        }

    }
     /// DELETE CLIENTS
     public function deleteClient($id_person, $id_org)
     {
         $gestionClient = new ClientServiceRepository;
 
         // verificar si el usuario existe para borrar
         $clientIsRegister = $gestionClient->getPersonById($id_person);
     
         if (count($clientIsRegister) == 0) {
             // no hay Datos
             return $this->sendError('Cliente no encontrado', 'Ningun registro insertado en esta tabla.');
         } else {
             $clientIsRegister = $gestionClient->deleteClient($id_person, $id_org);
             return $this->sendResponse('Cliente  Eliminado', 'Cliente dado de baja');
 
         }
     }
}
