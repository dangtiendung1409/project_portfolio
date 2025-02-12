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
          getApprovedPhotos: `${baseUrl}/approved-photos`,
          deletePhoto: (photo_id) => `${baseUrl}/photos/${photo_id}`,

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
          // add photo
          addPhoto : `${baseUrl}/add-photos`,
          //edit photo
          getPhoto: (photo_id) => `${baseUrl}/photo/${photo_id}`,
          editPhoto: (photo_id) => `${baseUrl}/edit-photo/${photo_id}`,

          // categories and tags
          getCategories: `${baseUrl}/categories`,
          getTags: `${baseUrl}/tags`,

          // profile user
          getUserByUserName: (username) => `${baseUrl}/user-by-username/${username}`,
          getPhotosByUserName: (username) => `${baseUrl}/photos-by-username/${username}`,
          getGalleriesByUserName: (username) => `${baseUrl}/galleries-by-username/${username}`
      }
}
export default getUrlList;
