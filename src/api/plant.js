import axios from 'axios';

const API = 'https://my.api.mockaroo.com';
export function getFlower() {
  return axios.get(API+'/hoa?key=a1852140')
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to fetch users:', error);
      throw error;
    });
}


export function delFlower(id) {
  return axios.get(`${API}/hoa/xoa/${id}?key=a1852140&__method=DELETE`)
    .then(response => response.data)
    .catch(error => {
      console.error('Failed to delete flower:', error);
      throw error;
    });
}


