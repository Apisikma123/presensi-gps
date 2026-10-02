'use strict';

export function checkStatusCode(requestParams, response, context, ee, next) {
  const code = response.statusCode;

  if (code === 508) {
    ee.emit('counter', 'http.codes.508', 1);
  }

  if (code >= 500) {
    ee.emit('counter', 'errors.5xx', 1);
  }

  if (code >= 400 && code < 500) {
    ee.emit('counter', 'errors.4xx', 1);
  }

  if (code === 429) {
    ee.emit('counter', 'errors.throttled', 1);
  }

  if (code === 504 || code === 408) {
    ee.emit('counter', 'errors.timeout', 1);
  }

  return next();
}

export function captureClockInResult(requestParams, response, context, ee, next) {
  const code = response.statusCode;

  ee.emit('counter', 'attendance.clockin.total', 1);

  if (code >= 200 && code < 300) {
    ee.emit('counter', 'attendance.clockin.success', 1);
  } else {
    ee.emit('counter', 'attendance.clockin.failed', 1);
  }

  return checkStatusCode(requestParams, response, context, ee, next);
}

export function logResponseMetrics(requestParams, response, context, ee, next) {
  return next();
}
