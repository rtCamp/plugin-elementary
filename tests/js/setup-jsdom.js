import '@testing-library/jest-dom/vitest';
import { cleanup } from '@testing-library/react';
import { afterEach } from 'vitest';

// Testing Library registers this itself only when Vitest globals are on.
afterEach( cleanup );
