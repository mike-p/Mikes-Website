Mikes-Website
=============
A copy of my simple website

## Testing

This project uses Playwright for end-to-end testing. After making changes, run tests to verify everything works:

### Setup
```bash
npm install
npx playwright install
```

### Run Tests
```bash
npm test                    # Run all tests
npm run test:ui            # Interactive test UI
npm run test:mobile        # Mobile tests only
npm run test:desktop       # Desktop tests only
```

See `tests/README.md` for more details.
