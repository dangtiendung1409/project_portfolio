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
          getPhotoDetail : ''+baseUrl+'/getPhotoDetail',
          getLikedPhotos: '' + baseUrl + '/liked-photos',
          updateProfile: baseUrl + '/update-profile',
          changePassword: baseUrl + '/change-password',
      }
}
export default getUrlList;
