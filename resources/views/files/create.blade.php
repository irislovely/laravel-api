<form action="/api/files" method="post" enctype="multipart/form-data">
    @csrf
    <div>
        <input type="text" name="name" id="" placeholder="Name">
    </div>

    <div>
        <select name="provider_id" id="">
            <option value="1">Google</option>
            <option value="2">Snapchat</option>
        </select>
    </div>
        
    <div>
        <input type="file" name="file" id="">
    </div>

    <button type="submit">Submit</button>
</form>