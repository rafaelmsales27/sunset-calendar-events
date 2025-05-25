<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Form</title>

    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</head>

<body class="antialiased bg-gray-200 dark:bg-gray-900">

    <div class="container mx-auto mt-10">
        <div class="flex justify-center">
            <div
                class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
                <h1 class="text-2xl mb-5 text-center text-gray-600 dark:text-gray-300 font-semibold">
                    Select the address for your sunset events
                </h1>

                <form class="max-w-sm mx-auto" method="POST" action="/form">
                    @csrf

                    <label for="country"
                        class="block mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Country</label>
                    <div class="mb-5">
                        <select name="country" id="country"
                            class="form-select w-full p-2.5 rounded border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (isset($test))
                        <p class="text-sm text-gray-500 dark:text-gray-400">Test: {{ $test }}</p>
                    @endif

                    <button type="submit"
                        class="mt-5 w-full text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                        Submit Address
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
