import mitt from 'mitt';

export const FILE_UPLOAD_EVENT = 'file-upload';
export const FOLDER_UPLOAD_EVENT = 'folder-upload';

export const emitter = mitt();