import React from 'react'
import './Reclamo.scss'
import FormReclamo from '../../components/FormReclamo/FormReclamo';
import DialogProvider from '../../context/DialogProvider';


function Reclamo(props) {
  const {credentials, onLogOut} = props
    return (
      <DialogProvider>
        <div className="reclamo">
            <div className="reclamo-label">
                Envía un nuevo reclamo desde la plataforma, ingresa el correo electrónico a la persona que recibirá el correo.
            </div>
            <FormReclamo
            credentials = {credentials}
            onLogOut = {onLogOut}
            />
        </div>
      </DialogProvider>


    )
}

export default Reclamo
