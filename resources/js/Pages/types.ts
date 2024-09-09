// types.ts
export interface PostImage {
    id: number;
    file: string;
    // Otros campos que puedan estar presentes en postImage
}

export interface Post {
    id: number;
    title: string;
    comment: string;
    postImages: PostImage[];
    // Otros campos que puedan estar presentes en post
}
