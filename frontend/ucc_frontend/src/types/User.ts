export interface User{
    readonly id: number
    name: string,
    email: string,
    role: 'user' | 'agent'
}