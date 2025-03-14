export function getUrlList()
{
const baseUrl = 'http://127.0.0.1:8000/api';
      return {
          // home page
          getPhotoData : `${baseUrl}/getPhotoData`,
          getFollowData : `${baseUrl}/getFollowData`,
          getTopLikedPhotos: `${baseUrl}/top-liked-photos`,
          getTopUsersWithPhotos: `${baseUrl}/top-users-with-photos`,
          getTopCategories: `${baseUrl}/top-categories`,
          getTopLikedGalleries: `${baseUrl}/top-liked-galleries`,
          getRecentFollowedPhotos: `${baseUrl}/recent-followed-photos`,
          getRecentFollowedGalleries: `${baseUrl}/recent-followed-galleries`,

          // like
          getLikedPhotos: `${baseUrl}/liked-photos`,
          likePhoto: `${baseUrl}/like-photo`,
          deleteLike: (like_id) => `${baseUrl}/like/${like_id}`,
          unlikePhoto: `${baseUrl}/unlike-photo`,
          likeGallery: `${baseUrl}/like-gallery`,
          unlikeGallery: `${baseUrl}/unlike-gallery`,

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
          getCommentsByPhotoToken: `${baseUrl}/comments`,
          getPhotoLikes: (token) => `${baseUrl}/photo-likes/${token}`,
          postComment: `${baseUrl}/comments`,
          deleteComment: (commentId) => `${baseUrl}/comments/${commentId}`,
          getRelatedPhotos: (token) => `${baseUrl}/related-photos/${token}`,
          getRelatedGalleries: (token) => `${baseUrl}/related-galleries/${token}`,

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
          getGalleriesByUserName: (username) => `${baseUrl}/galleries-by-username/${username}`,
          getGalleryDetailUser: (galleries_code) => `${baseUrl}/gallery-details-user/${galleries_code}`,
          getTotalLikes: (username) => `${baseUrl}/total-likes/${username}`,
          // category
          getPhotosByCategorySlugs: (slugs) => `${baseUrl}/categories/photos?slugs=${slugs}`,

          // search photos
          searchPhotos: `${baseUrl}/search-photos`,

          // follow/unfollow
          followUser: `${baseUrl}/follow`,
          unfollowUser: (following_id) => `${baseUrl}/unfollow/${following_id}`,
          getFollowingList: `${baseUrl}/following-list`, // danh sách người dùng đang login follow
          getFollowersList: `${baseUrl}/followers-list`, // danh sách follow người dùng đang login
          getFollowingUser: (username) => `${baseUrl}/getFollowingUser/${username}`, // danh sách username đang follow
          getFollowersUser: (username) => `${baseUrl}/getFollowersUser/${username}`, // danh sách theo dõi username

          // block/unblock
          blockUser: `${baseUrl}/block`,
          unblockUser: `${baseUrl}/unblock`,
          getBlockedUsers: `${baseUrl}/blocked-users`,

          // report
          reportViolation: `${baseUrl}/report`,

          // contact
          sendContact: `${baseUrl}/contact`,

          // blogs
          getLatestBlogs: `${baseUrl}/blogs/latest`, // Lấy 5 blog gần đây nhất
          getOlderBlogs: `${baseUrl}/blogs/older`, // Lấy blog có thời gian sau 7 ngày
          getBlogDetails: (slug) => `${baseUrl}/blog/details/${slug}`,
      }
}
export default getUrlList;
