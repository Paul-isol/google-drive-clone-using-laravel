export type FileData = {
    id: number;
    name: string;
    slug: string | null;
    parent_id: number | null;
    is_folder: boolean | number;
    mime: string | null;
    size: number | null;
    path: string | null;
    owner: string | null;
    created_by: number;
    updated_by: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
};
