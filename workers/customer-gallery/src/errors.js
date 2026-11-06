/**
 * Errors shared by the sync modules.
 */

/** Thrown when a run has used up its allowance of outside requests. */
export class BudgetExhausted extends Error {
	constructor() {
		super('request limit for this run reached');
		this.name = 'BudgetExhausted';
	}
}

/** An error reply from Facebook's Graph API. */
export class FacebookError extends Error {
	/**
	 * @param {{message?: string, code?: number}|undefined} fbError The "error" object Facebook sent, if any.
	 * @param {number} httpStatus HTTP status of the reply.
	 */
	constructor(fbError, httpStatus) {
		const code = fbError && fbError.code;
		let message = fbError && fbError.message ? `Facebook: ${fbError.message}` : `Facebook answered HTTP ${httpStatus}`;
		if (code === 190) {
			message += ' (the token is invalid or has expired; make a new one and run: npx wrangler secret put FB_PAGE_TOKEN)';
		}
		super(message);
		this.name = 'FacebookError';
		this.code = code;
		this.httpStatus = httpStatus;
	}
}

/**
 * A one-line, token-free description of an error for status.json and logs.
 *
 * @param {unknown} err
 * @returns {string}
 */
export function describeError(err) {
	if (err instanceof Error) {
		return err.message;
	}
	return String(err);
}
