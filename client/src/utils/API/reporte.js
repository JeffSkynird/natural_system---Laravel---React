
import {encriptarJson,desencriptarJson} from '../security'
import {ENTRYPOINT,LARAVEL_SGI} from '../../config/API'
const axios = require('axios');
export const downloadFiles = (data,store) => {
    const { usuario, cargarUsuario, mostrarNotificacion, mostrarLoader } = store;
   
  
    let url = ENTRYPOINT+"reporte";
    let setting = {
      method: "GET",
      params:data,
      url: url,
      responseType: 'blob',
      headers: {
        Accept: "application/json",
      }
    };
    mostrarLoader(true);
  
    axios(setting)
      .then((res) => {
        let response = res.data;
        if(res.data.type!="error"){
          const url = window.URL.createObjectURL(new Blob([response]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'reporte.pdf'); //or any other extension
          link.target="_blank"
          document.body.appendChild(link);
          link.click();
          mostrarLoader(false);
        
        }else{
        
          mostrarLoader(false);
          
        }
        
      })
      .catch((error) => {
        mostrarLoader(false);
     
      });
  };