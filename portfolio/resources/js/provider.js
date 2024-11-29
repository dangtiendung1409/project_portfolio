export function getUrlList()
{
const baseUrl = 'http://127.0.0.1:8000/api';
      return {
          getPhotoData : ''+baseUrl+'/getPhotoData',
          getFollowData : ''+baseUrl+'/getFollowData',
          login : ''+baseUrl+'/login',
          register : ''+baseUrl+'/register',
          logout : ''+baseUrl+'/logout',
          refreshToken: ''+baseUrl+'/refresh-token',
          getUser : ''+baseUrl+'/user',

      }
}
export default getUrlList;
