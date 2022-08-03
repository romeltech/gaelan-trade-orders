import axios from "axios";

let apiUrl = "";
apiUrl = "http://127.0.0.1:8000";
// apiUrl = "http://loginextuatadmin.grandiose.ae";
// apiUrl = process.env.REACT_APP_BACKEND_API_URL;

const gagUserClient = axios.create({
  baseURL: apiUrl,
//   withCredentials: true,
});

export default gagUserClient;
