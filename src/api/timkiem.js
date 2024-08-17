import axios from 'axios';
const API = 'http://127.0.0.1:8000';


export function nhapTimKiem(data) {
    return axios.post(`${API}/nhaptutimkiem.php`, data)
        .then(response => response.data)
        .catch(error => {
            console.error('Failed to add search term:', error);
            throw error;
        });
  }


  export function ketQuaTimKiem() {
    return axios.get(`${API}/timkiembaiviet.php`)
        .then(response => response.data)
        .catch(error => {
            console.error('Failed to add search term:', error);
            throw error;
        });
  }
