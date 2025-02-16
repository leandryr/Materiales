import React, { useState } from 'react';
import { Tabs, Tab } from '@material-ui/core';
import FormNuevoRegistro from '../../components/FormNuevoRegistro/FormNuevoRegistro';
import CargarExcel from '../../components/CargarExcel/CargarExcel';
import DialogProvider from '../../context/DialogProvider';
import './NuevoRegistro.scss';



function NuevoRegistro(props) {
    const [selectedTab, setSelectedTab] = useState(0);
    const {localidades, transportistas,rutas, plantas, areas, proveedores, tipos, descripciones, credentials} = props


    const handleChange = (event, newValue) => {
        setSelectedTab(newValue);
    }
    return (
        <DialogProvider>
            <div className="nuevo_registro">
            <Tabs value={selectedTab} onChange={handleChange}>
                    <Tab label="Nuevo Registro"></Tab>
                    <Tab label="Cargar Excel"></Tab>
                </Tabs>
                {selectedTab === 0 && <FormNuevoRegistro
                  localidades = {localidades}
                  transportistas = {transportistas}
                  rutas = {rutas}
                  plantas = {plantas}
                  areas = {areas}
                  proveedores = {proveedores}
                  credentials = {credentials}
                  tipos = {tipos}
                  descripciones = {descripciones}
                  />}
                {selectedTab === 1 && <CargarExcel
                    credentials = {credentials}
                    />}
            </div>
        </DialogProvider>
    )
}

export default NuevoRegistro
