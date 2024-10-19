export function getUrlList()
{
const baseUrl = 'http://127.0.0.1:8000/api';
      return {
          getPhotoData : ''+baseUrl+'/getPhotoData',

      }
}
export default getUrlList;
