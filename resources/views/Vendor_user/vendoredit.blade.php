@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <form action="{{ url('/vendor_user/edit/upload/' . $vendors->id) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Nama Vendor</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nama_vendor" name="nama_vendor"
                        value="{{ $vendors->nama_vendor }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Telpon</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="telpon" name="telpon" value="{{ $vendors->telpon }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"value="{{ $vendors->alamat }}">{{ $vendors->alamat }}</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Bahan</label>
                <div class="col-sm-10">
                    @foreach ($bahan as $item)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $item->id }}" name="bahan[]"
                                id="bahan_{{ $item->id }}" {{ in_array($item->id, $selectedBahan) ? 'checked' : '' }}>
                            <!-- Cek jika produk dipilih -->
                            <label class="form-check-label" for="bahan_{{ $item->id }}">
                                {{ $item->nama_produk }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="simpan">Update</button>
                <a href="{{ route('Vendor') }}" class="btn btn-danger">Batal</a>
            </div>
        </form>
    </div>
@endsection
