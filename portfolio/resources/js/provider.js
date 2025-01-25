export function getUrlList()
{
const baseUrl = 'http://127.0.0.1:8000/api';
      return {
          // home page
          getPhotoData : `${baseUrl}/getPhotoData`,
          getFollowData : `${baseUrl}/getFollowData`,

          // like
          getLikedPhotos: `${baseUrl}/liked-photos`,
          likePhoto: `${baseUrl}/like-photo`,
          deleteLike: (photo_id) => `${baseUrl}/like/${photo_id}`,
          unlikePhoto: `${baseUrl}/unlike-photo`,

          //notifications
          getUserNotifications: `${baseUrl}/notifications`,
          markNotificationAsRead: `${baseUrl}/notifications/mark-as-read`,

          // auth user
          login : `${baseUrl}/login`,
          register : `${baseUrl}/register`,
          logout : `${baseUrl}/logout`,
          refreshToken: `${baseUrl}/refresh-token`,
          getUser : `${baseUrl}/user`,

          // account user
          updateProfile: `${baseUrl}/update-profile`,
          changePassword: `${baseUrl}/change-password`,

          // gallery
          getGallery: `${baseUrl}/galleries`,
          addPhotoToGallery: `${baseUrl}/gallery/add-photo`,
          getGalleryDetails: `${baseUrl}/gallery-details`,
          addGallery: `${baseUrl}/add-gallery`,
          editGallery: `${baseUrl}/update-gallery`,
          deleteGallery: `${baseUrl}/delete-gallery`,
          deletePhotoFromGallery: (galleries_code, photo_id) => `${baseUrl}/gallery/${galleries_code}/photo/${photo_id}`,
          // photo details
          getPhotoDetail : `${baseUrl}/getPhotoDetail`,
      }
}
export default getUrlList;
