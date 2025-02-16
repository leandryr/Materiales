
function fetchCall(type, url, options) {
  let headers = {
  };

  if(type === 'form'){
     headers = {
    };

  }
  if(type === 'login'){
     headers = {

        'Content-Type': 'application/json',
        'X-Csrf-Token': window.CSRF_TOKEN,
    };
  }
  if(type === 'json'){
     headers = {
        'Access-Control-Allow-Origin' : '*',
        'Content-Type': 'undefined',
        'X-Csrf-Token': window.CSRF_TOKEN,
    };
  }

    if (options && options.headers) {
        headers = {...options.headers, ...headers};
        delete options.headers;
    }

    return fetch(url, Object.assign({
        credentials: 'same-origin',
        headers: headers,
    }, options))
        .then(checkStatus)
        .then(response => {
            if (response.headers.has('X-CSRF-TOKEN')) {
                window.CSRF_TOKEN = response.headers.get('X-CSRF-TOKEN');
            }

            // decode JSON, but avoid problems with empty responses
            return response.text()
                .then(text => text ? JSON.parse(text) : '')
        });
}

function checkStatus(response) {
    if (response.status >= 200 && response.status < 300) {
        return response;
    }
    const error = new Error(response.statusText);
    error.response = response;
    throw error
}


/*
*  Api Login
*
*
*/
export function login(credentials) {
    return fetchCall('login' , '/login', {
        method: 'POST',
        body: JSON.stringify(credentials)
    });
}

export function newUser(credentials, data) {
    return fetchCall('json','/api/newUser/'+credentials.username + '/' + credentials.token, {
        method: 'POST',
        body: JSON.stringify(data)
    });
  }

  export function sendEmail(credentials, data) {
      return fetchCall('form','/api/sendEmail/'+credentials.username + '/' + credentials.token, {
          method: 'POST',
          body: data
      });
    }

    export function deleteFile(credentials,enlace) {
        return fetchCall('json', '/api/deleteFile/'+credentials.username + '/' + credentials.token +'/'+
        enlace
      )
            .then(data => data);
    }

    export function getUsers(credentials) {
        return fetchCall('json', '/api/getUsers/' +credentials.username + '/' + credentials.token)
            .then(data => data.validation);
    }

    export function editUser(credentials, data) {
        return fetchCall('json','/api/editUser/'+credentials.username + '/' + credentials.token, {
            method: 'POST',
            body: JSON.stringify(data)
        });
      }

      export function editReport(credentials, data) {
          return fetchCall('json','/api/editReport/'+credentials.username + '/' + credentials.token, {
              method: 'POST',
              body: JSON.stringify(data)
          });
        }

        export function editDocumentado(credentials, data) {
            return fetchCall('json','/api/editDocumentado/'+credentials.username + '/' + credentials.token, {
                method: 'POST',
                body: JSON.stringify(data)
            });
          }

      export function deleteUser(credentials,data ) {
        return fetchCall('json','/api/deleteUser/'+credentials.username + '/' + credentials.token + '/' + data, {
            method: 'DELETE',
        });
      }
      export function deactivateUser(credentials,data ) {
        return fetchCall('json','/api/deactivateUser/'+credentials.username + '/' + credentials.token + '/' + data, {
            method: 'GET',
        });
      }

      export function newReport(credentials, data) {
          return fetchCall('json','/api/newReport/'+credentials.username + '/' + credentials.token, {
              method: 'POST',
              body: JSON.stringify(data)
          });
        }

        export function getReportes(credentials,data) {
          return fetchCall('json','/api/getReportes/'+credentials.username + '/' + credentials.token, {
              method: 'POST',
              body: JSON.stringify(data)
          });
        }

        export function getDocumentados(credentials) {
            return fetchCall('json', '/api/getDocumentados/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getExcelSemanal(credentials, estatus, tipoReporte) {
            return fetchCall('json','/api/getExcelSemanal/'+credentials.username + '/' + credentials.token +'/'+
          estatus +'/' + tipoReporte
          )
                .then(data => data);
        }

        export function documentarReporte(credentials, reporteId) {
            return fetchCall('json','/api/documentarReporte/'+credentials.username + '/' + credentials.token +'/'+
            reporteId
          )
                .then(data => data);
        }

        export function getExcelDocumentados(credentials) {
            return fetchCall('json','/api/getExcelDocumentados/'+credentials.username + '/' + credentials.token
          )
                .then(data => data);
        }

        export function getExcelDocumentado(credentials,documento) {
            return fetchCall('json','/api/getExcelDocumentado/'+credentials.username + '/' + credentials.token +'/'+
            documento
          )
                .then(data => data);
        }

        export function getExcelReporte(credentials,reporte) {
            return fetchCall('json','/api/getExcelReporte/'+credentials.username + '/' + credentials.token +'/'+
            reporte
          )
                .then(data => data);
        }

        export function getReporte(credentials,reporte_id) {
            return fetchCall('json', '/api/getReporte/' +credentials.username + '/' + credentials.token + '/'+ reporte_id)
                .then(data => data.validation);
        }
        export function getDocumentado(credentials,reporte_id) {
            return fetchCall('json', '/api/getDocumentado/' +credentials.username + '/' + credentials.token + '/'+ reporte_id)
                .then(data => data.validation);
        }

        export function getTransportistas(credentials) {
            return fetchCall('json', '/api/getTransportistas/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getLocalidades(credentials) {
            return fetchCall('json', '/api/getLocalidades/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getRutas(credentials) {
            return fetchCall('json', '/api/getRutas/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getPlantas(credentials) {
            return fetchCall('json', '/api/getPlantas/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }
        export function getAreas(credentials) {
            return fetchCall('json', '/api/getAreas/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getProveedores(credentials) {
            return fetchCall('json', '/api/getProveedores/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getTipos(credentials) {
            return fetchCall('json', '/api/getTipos/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function getDescripciones(credentials) {
            return fetchCall('json', '/api/getDescripciones/' +credentials.username + '/' + credentials.token)
                .then(data => data.validation);
        }

        export function newRegistro(credentials, data) {
            return fetchCall('json','/api/newRegistro/'+credentials.username + '/' + credentials.token, {
                method: 'POST',
                body: JSON.stringify(data)
            });
          }

          export function uploadFile(credentials, data) {
              return fetchCall('form','/api/uploadFile/'+credentials.username + '/' + credentials.token, {
                  method: 'POST',
                  body: data
              });
            }
        export function getExcelRegistros(credentials, data) {
            return fetchCall('form','/api/getExcelRegistros/'+credentials.username + '/' + credentials.token, {
                method: 'POST',
                body: JSON.stringify(data)
            });
          }
        export function getRegistrosBusqueda(credentials, data) {
            return fetchCall('json','/api/getRegistrosBusqueda/'+credentials.username + '/' + credentials.token, {
                method: 'POST',
                body: JSON.stringify(data)
            });
          }

          export function getRegistro(credentials,registro_id) {
              return fetchCall('json', '/api/getRegistro/' +credentials.username + '/' + credentials.token+
            '/'+registro_id)
                  .then(data => data.validation);
          }

          export function getExcelRegistro(credentials,registro_id) {
              return fetchCall('json', '/api/getExcelRegistro/' +credentials.username + '/' + credentials.token+
            '/'+registro_id)
                  .then(data => data.validation);
          }

          export function editRegistro(credentials, data) {
              return fetchCall('json','/api/editRegistro/'+credentials.username + '/' + credentials.token, {
                  method: 'POST',
                  body: JSON.stringify(data)
              });
            }

            export function ingresarDocumentado(credentials,id) {
                return fetchCall('json', '/api/ingresarDocumentado/'+credentials.username + '/' + credentials.token +'/'+
                id
              )
                    .then(data => data);
            }
