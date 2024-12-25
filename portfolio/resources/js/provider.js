export function getUrlList()
{
const baseUrl = 'http://127.0.0.1:8000/api';
      return {
          // home page
          getPhotoData : ''+baseUrl+'/getPhotoData',
          getFollowData : ''+baseUrl+'/getFollowData',
          likePhoto: `${baseUrl}/like-photo`,
          unlikePhoto: `${baseUrl}/unlike-photo`,

          // auth user
          login : ''+baseUrl+'/login',
          register : ''+baseUrl+'/register',
          logout : ''+baseUrl+'/logout',
          refreshToken: ''+baseUrl+'/refresh-token',
          getUser : ''+baseUrl+'/user',

          // account user
          getLikedPhotos: '' + baseUrl + '/liked-photos',
          updateProfile: baseUrl + '/update-profile',
          changePassword: baseUrl + '/change-password',

          // photo details
          getPhotoDetail : ''+baseUrl+'/getPhotoDetail',
      }
}
export default getUrlList;
