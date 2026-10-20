export const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api';
export const STORAGE_BASE_URL = process.env.NEXT_PUBLIC_STORAGE_URL || API_BASE_URL.replace(/\/api\/?$/, '/storage/');

interface FetchOptions extends RequestInit {
  params?: Record<string, string | number | boolean>;
}

export class ApiError extends Error {
  status: number;
  data: any;

  constructor(status: number, message: string, data: any) {
    super(message);
    this.status = status;
    this.data = data;
  }
}

export const apiClient = {
  get: (url: string, options?: FetchOptions) => request(url, { ...options, method: 'GET' }),
  post: (url: string, data?: any, options?: FetchOptions) => 
    request(url, { 
      ...options, 
      method: 'POST',
      body: data instanceof FormData ? data : JSON.stringify(data),
      headers: data instanceof FormData ? undefined : { 'Content-Type': 'application/json' }
    }),
  put: (url: string, data?: any, options?: FetchOptions) => 
    request(url, { 
      ...options, 
      method: 'PUT',
      body: data instanceof FormData ? data : JSON.stringify(data),
      headers: data instanceof FormData ? undefined : { 'Content-Type': 'application/json' }
    }),
  delete: (url: string, options?: FetchOptions) => request(url, { ...options, method: 'DELETE' }),
};

async function request(endpoint: string, options: FetchOptions = {}) {
  const { params, headers: customHeaders, ...restOptions } = options;
  
  // Construct URL
  const url = new URL(endpoint.startsWith('http') ? endpoint : `${API_BASE_URL}${endpoint}`);
  
  if (params) {
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        url.searchParams.append(key, String(value));
      }
    });
  }

  // Construct Headers
  const headers = new Headers(customHeaders);
  
  // Automatically inject token if we are on the client side
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('access_token');
    if (token) {
      headers.set('Authorization', `Bearer ${token}`);
    }
  }

  // Default Accept header
  if (!headers.has('Accept')) {
    headers.set('Accept', 'application/json');
  }

  // Tell Laravel this is an AJAX request
  if (!headers.has('X-Requested-With')) {
    headers.set('X-Requested-With', 'XMLHttpRequest');
  }

  try {
    const response = await fetch(url.toString(), {
      ...restOptions,
      headers,
    });

    // We can't immediately read JSON if the response is 204 No Content
    if (response.status === 204) {
      return null;
    }

    let data;
    try {
      data = await response.json();
    } catch (e) {
      data = null;
    }

    if (!response.ok) {
      // Intercept 401 Unauthorized
      if (response.status === 401) {
        if (typeof window !== 'undefined') {
          localStorage.removeItem('access_token');
          // Optionally emit an event or redirect to login
          window.dispatchEvent(new Event('unauthorized'));
        }
      }
      
      throw new ApiError(
        response.status,
        data?.message || `Request failed with status ${response.status}`,
        data
      );
    }

    // Handle 200 OK but success is false
    if (data && data.success === false) {
      throw new ApiError(
        response.status,
        data.message || 'An error occurred',
        data
      );
    }

    return data;
  } catch (error) {
    if (error instanceof ApiError) {
      throw error;
    }
    // Network errors or parsing errors
    throw new Error(error instanceof Error ? error.message : 'An unknown error occurred');
  }
}
