@extends("admin/layout")

@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List photo</li>
            </ul>
        </div>
    </section>

    <section class="section main-section">
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
                    List photos for {{ $user->username }}
                </p>
                <form class="card-header-icon" method="get" onchange="this.submit()">
                    <select style="margin-left:-10px; padding: 5px 10px; border: 1px solid #F1F1F1" name="size">
                        <option value="10" {{ request('size') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('size') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('size') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('size') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <input type="hidden" name="page" value="{{ request('page', 1) }}">
                </form>
            </header>

            <div class="card-content">
                @if($photos->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No images exist
                    </div>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Upload Date</th>
                            <th>Privacy Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($photos as $photo)
                            <tr>
                                <td>{{ $photo->title }}</td>
                                <td>{{ $photo->description }}</td>
                                <td>
                                    <img src="{{ asset($photo->image_url) }}" width="150" height="150" style="cursor: pointer;" onclick="showModal(this)">
                                </td>
                                <td>{{ $photo->location }}</td>
                                <td>{{ $photo->category->category_name ?? 'N/A' }}</td>
                                <td>{{ date('d-m-Y', strtotime($photo->upload_date)) }}</td>
                                <td class="{{ $photo->privacy_status == 1 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $photo->privacy_status == 1 ? 'Private' : 'Public' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                {{ $photos->appends(['size' => request('size')])->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
    </div>

    <script>
        function showModal(img) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("imgModal");
            modal.style.display = "block";
            modalImg.src = img.src;
        }

        document.querySelector(".close").onclick = function() {
            document.getElementById("myModal").style.display = "none";
        };
    </script>
@endsection
