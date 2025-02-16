import React, {useEffect, useState} from 'react';
import FilterUsuarios from '../../components/FilterUsuarios/FilterUsuarios';
import HistorialUsuarios from '../../components/HistorialUsuarios/HistorialUsuarios';
import DialogProvider from '../../context/DialogProvider';
import './Usuario.scss';

import { newUser , getUsers } from '../../api/api.js';

function Usuarios(props) {
  const [usuarios, handleUsuarios] = useState(['']);
  const {credentials} = props;

  const handleUpdateList = data => {
    handleUsuarios( [...usuarios.filter(usuario => usuario.id !==data.id),data ]);
  }

  const handleDeleteUser = (user_id) => {
    handleUsuarios([...usuarios.filter(usuario => usuario.id !==user_id) ]);
  }

  const handleDeactivateUser = (user_id) => {
    let arr= usuarios;
    arr = arr.map((u)=> {
      if(u.id === parseInt(user_id)){
        if(u.activo){
          u.activo = false;
        }else{
          u.activo = true;
        }
      }
      return u;
    });
    handleUsuarios(arr);
  }

  const handleDialogOpen = (value) => {
    setDialogOpen(value);
  }



  const handleNuevoUsuario = data => {
      handleUsuarios([...usuarios, data] );
  }

useEffect(()=>{
  getUsers(credentials)
  .then(datos => {
    if(datos.status){
      handleUsuarios(datos.items);
    }
  })
  .catch(e =>{
    console.log(e);
  })
},[]);

    return (
        <DialogProvider>
            <div className="usuario">
                <FilterUsuarios
                  onUsuarioNuevo = {handleNuevoUsuario}
                  credentials = {credentials}
                ></FilterUsuarios>
                <HistorialUsuarios
                usuarios = {usuarios}
                credentials = {credentials}
              onUpdateList = {handleUpdateList}
              onDeleteUser = {handleDeleteUser}
              onDeactivateUser = {handleDeactivateUser}
                >
                </HistorialUsuarios>
            </div>
        </DialogProvider>
    )
}

export default Usuarios
