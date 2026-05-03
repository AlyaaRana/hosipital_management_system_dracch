class File extends Model
{
    protected $fillable = ['path', 'filename', 'mime_type', 'size', 'fileable_id', 'fileable_type'];

    public function fileable()
    {
        return $this->morphTo();
    }
}