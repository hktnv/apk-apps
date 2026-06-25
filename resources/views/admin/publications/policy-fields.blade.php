<label>Kanal</label>
<select name="channel" required>
    <option value="stable">stable</option>
    <option value="beta">beta</option>
    <option value="internal">internal</option>
</select>

<label><input type="checkbox" name="force_update" value="1" style="width: auto"> Force update</label>

<label>Minimum supported version code</label>
<input type="number" min="0" name="minimum_supported_version_code" value="{{ old('minimum_supported_version_code', 0) }}" required>

<label>Açıklama</label>
<textarea name="comment" required>{{ old('comment') }}</textarea>
