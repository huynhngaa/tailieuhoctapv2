import axios from 'axios';
const API = 'http://127.0.0.1:8000';
export function getListVaiTro() {
  return axios.get(API+'/listvaitro.php')
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch users:', error);
      throw error;
    });
}

export function addVaiTro(vaiTro) {
  return axios.post(`${API}/vaitroadd.php`, vaiTro)
      .then(response => response.data)
      .catch(error => {
          console.error('Failed to add Khoi Lop:', error);
          throw error;
      });
}

export function updateVaiTro(vaiTro) {
  return axios.post(`${API}/vaitroedit.php`, vaiTro)
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to update Khoi Lop:', error);
      throw error;
    });
}


export function deleteVaiTro(vt_ma) {
  return axios.post(`${API}/vaitrodelete.php`, { vt_ma })
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to delete Khoi Lop:', error);
      throw error;
    });
}