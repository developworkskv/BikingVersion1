import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, ParamMap, Params } from '@angular/router';
import { UserService } from 'app/_services/user.service';
import { ToastService } from 'app/_services/toast.service';
import { FormBuilder, FormGroup, NgForm, Validators } from '@angular/forms';
import { TypeUsersService } from 'app/_services/type-users.service';
import {Location} from '@angular/common';

@Component({
  selector: 'app-user-details',
  templateUrl: './user-details.component.html',
  styleUrls: ['./user-details.component.scss']
})
export class UserDetailsComponent implements OnInit {
  administrator:any;
  adminForm: FormGroup;
  id_org: any;
  tipesUser: any;
  personId: number;
  // COMPONENTE DE DETALLES DEL USER-ADMINISTRADOR
  constructor(private route: ActivatedRoute,
     public _user:UserService,
     private toast: ToastService,
     private formBuilder: FormBuilder,
     private _typeUser: TypeUsersService,
     public router: Router
     ) { 
      this.id_org = localStorage.getItem('bd_org');
     }

  ngOnInit() {
    // Obtener parametros
    this.route.params.subscribe(
      (param: Params) =>{
        console.log(param);
        
        const idPerson = param.id_admin;
        //consumir servicio detalles by id
        this._user.detailsAdministrator(idPerson).subscribe(
          resp=>{
            //console.log(resp);
            if(resp['status'] == 1){
              this.administrator = resp['data'][0];
              console.log("DATA Details: " + JSON.stringify(this.administrator));
              this.crearFormularios();

            }
          }
        );        
      }
    );
      this.typesAdmin();
  }



  crearFormularios(){
    //CARGAR LOS DATOS Q QUERMEOS MODIFICAR;
    // Creamos el formulario  
    this.adminForm = this.formBuilder.group({
    //  bd_organization_id: [localStorage.getItem('bd_org'), Validators.required],
    //  bd_type_users_id: ['1', Validators.required],
    name: [this.administrator['name'], Validators.required],
    lastName: [this.administrator['lastName'], Validators.required],
    email: [this.administrator['email'], Validators.required],
    fechaNacimiento: [this.administrator['birth_date'], Validators.required],
    gender: [this.administrator['gender'], Validators.required],
    dni: [this.administrator['dni'], Validators.required],
    type_user: [this.administrator['type_user'], Validators.required],
    bd_type_users_id: [this.administrator['type_user'], Validators.required],
    // password: [this.administrator['password'], Validators.required],
   //  bd_organization_id: [this.id_org, Validators.required],
   // bd_type_users_id: ['', Validators.required],

  });
}

//TIPES ADMIN
typesAdmin(){
  this._typeUser.getTypesUser().subscribe(
    resp=>{
      console.log("A");
      if( resp['status'] == 1){
        this.tipesUser = resp['data'];
        console.log("LOS TIOPES " + this.tipesUser);
      }
    }
  );
  
}

// EDITAR ADMIN
onSubmit(adminForm: NgForm){ // 
  console.log(adminForm);
  
  // datos vacios en el formulario
  if (this.adminForm.invalid) {
    return this.toast.showNotification('top','right','danger','Completar los datos solicitados.');
  }
//consumir servicio POST CrearRuta/id_usuario
//let id_user:any =  localStorage.getItem('user_id');
this._user.updateAdmin(this.administrator.bd_persons_id, adminForm.value).subscribe(resp => {
   console.log(resp);
    if(resp['status'] == 1){
      this.toast.showNotification('top','right','success','Usuario editado correctamente.');
      this._user.getAllAdministrators();
    }else{
      this.toast.showNotification('top','right','danger','Ingresa los datos solicitados.');
    }
}, err => {
 //this.toastr.error('Servicio Ejecutado', err['data']);
});   
}


}
