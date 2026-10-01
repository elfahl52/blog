import './bootstrap';
import 'bootstrap';

const imageInput = document.querySelector('#image[type="file"]');
const imageForm = imageInput?.form;
let imageOptimized = false;

imageForm?.addEventListener('submit', async (event) => {
	const sourceFile = imageInput?.files?.[0];

	if (imageOptimized || !sourceFile || typeof createImageBitmap !== 'function') {
		return;
	}

	event.preventDefault();

	let bitmap;

	try {
		bitmap = await createImageBitmap(sourceFile);
		const maxDimension = 1600;
		const scale = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
		const canvas = document.createElement('canvas');
		canvas.width = Math.round(bitmap.width * scale);
		canvas.height = Math.round(bitmap.height * scale);

		const context = canvas.getContext('2d');
		context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);

		const optimizedBlob = await new Promise((resolve) => {
			canvas.toBlob(resolve, 'image/webp', 0.82);
		});

		if (optimizedBlob && optimizedBlob.size < sourceFile.size) {
			const baseName = sourceFile.name.replace(/\.[^.]+$/, '') || 'image';
			const optimizedFile = new File([optimizedBlob], `${baseName}.webp`, {
				type: 'image/webp',
				lastModified: Date.now(),
			});
			const transfer = new DataTransfer();
			transfer.items.add(optimizedFile);
			imageInput.files = transfer.files;
		}
	} catch {
	} finally {
		bitmap?.close();
	}

	imageOptimized = true;

	if (event.submitter) {
		imageForm.requestSubmit(event.submitter);
	} else {
		imageForm.requestSubmit();
	}
});
