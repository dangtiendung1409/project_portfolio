@extends("admin/layout")
@section("content")

<section class="is-hero-bar">
    <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
        <h1 class="title">
            Details pending product
        </h1>
    </div>
</section>
<section class="section main-section">

    <div class="card has-table">
        <header class="card-header">
            <p class="card-header-title">
                Details of {{ $photo->title }}
            </p>

            <a href="#" class="card-header-icon">
                <span class="icon"><i class="mdi mdi-reload"></i></span>
            </a>
        </header>
        <div class="card-content">
            <table class="table table-hover table-bordered js-copytextarea" cellpadding="0" cellspacing="0" border="0" id="sampleTable">
                <thead>
                <tr>
                    <th width="10"><input type="checkbox" ></th>
                    <th>id</th>
                    <th>title</th>
                    <th>description</th>
                    <th>image</th>
                    <th>upload date</th>
                    <th>location</th>
                    <th>user name</th>
                    <th>privacy_status</th>
                    <th>category name</th>
                    <th>tag</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th width="10"><input type="checkbox"></th>
                    <td>{{ $photo->id }}</td>
                    <td>{{ $photo->title }}</td>
                    <td>{{ $photo->description }}</td>
                    <td>
                        <img src="{{ asset('' . $photo->image_url) }}" width="450" height="450" style="cursor: pointer;" onclick="showModal(this)">
                    </td>
                    <td>{{ date('d-m-Y', strtotime($photo->upload_date)) }}</td>
                    <td>{{ $photo->location }}</td>
                    <td>{{ $photo->user->username }}</td>
                    <td>{{ $photo->privacy_status }}</td>
                    <td>{{ $photo->category->category_name }}</td>
                    <td>
                        {{ implode(', ', $photo->tags->pluck('tag_name')->toArray()) }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</section>
<!-- The Modal -->
<div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="imgModal">
    <div id="caption"></div>
</div>

<div style="display: flex; gap: 10px; margin-top: 10px;margin-right:17px; justify-content: flex-end;">
    @if($photo->photo_status == 'pending')
        <!-- Approve Button -->
        <form method="POST" action="{{ route('admin.updatePhotoStatus', $photo->id) }}">
            @csrf
            <input type="hidden" name="status" value="approved" />
            <button onclick="return confirm('Are you sure you want to approve this photo?')" type="submit" class="btn btn-success"
                    style="padding: 5px 10px; border-radius: 5px; background-color: green; color: white;">
                Approve
            </button>
        </form>

        <!-- Reject Button -->
        <form method="POST" action="{{ route('admin.updatePhotoStatus', $photo->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="rejected" />
            <button onclick="return confirm('Are you sure you want to reject this photo?')" type="submit" class="btn btn-danger"
                    style="padding: 5px 10px; border-radius: 5px; background-color: red; color: white;">
                Reject
            </button>
        </form>
    @endif
        <!-- Back Button -->
        <a href="{{ route('admin.photoPending') }}" class="btn btn-warning"
           style="padding: 5px 10px; border-radius: 5px; background-color: orange; color: white;">
            Back
        </a>
</div>
@endsection
