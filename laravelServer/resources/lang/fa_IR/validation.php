<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'گزینه :attribute باید تایید گردد.',
    'active_url'           => 'آدرس سایت :attribute صحیح نیست',
    'after'                => 'گزینه :attribute باید یک تاریخ بعد از  :date باشد.',
    'after_or_equal'       => 'گزینه :attribute باید بعد از/برابر با:date باشد',
    'alpha'                => 'گزینه :attribute تنها میتواند شامل کاراکتر های انگلیسی باشد',
    'alpha_dash'           => 'گزینه:attribute تنها متیواند شامل کاراکترهای انگلیسی,شماره, (-) و (_) باشد',
    'alpha_num'            => 'گزینه :attribute میتواند شامل اعداد و حروف انگلیسی باشد.',
    'array'                => 'گزینه :attribute باید یک آرایه باشد.',
    'before'               => 'گزینه :attribute باید یک تاریخ قبل از :date باشد.',
    'before_or_equal'      => 'گزینه :attribute باید برابر یا قبل از :date باشد.',
    'between'              => [
        'numeric' => 'گزینه  :attribute باید بین :min و :max باشد.',
        'file'    => ' حچم فایل :attribute میتواند حداقل :min و حداکثر :max باشد.',
        'string'  => 'تعداد کاراکترهای :attribute باید حداقل :min و حداکثر :max باشد.',
        'array'   => 'تعداد آرایه :attribute حداقل :min و حداکثر :max باشد',
    ],
    'boolean'              => 'گزینه :attribute باید به حالت true یا false باشد.',
    'confirmed'            => 'باید با :attribute موافق باشید ',
    'date'                 => 'گزینه تاریخ :attribute باید به صورت صحیح وارد شود',
    'date_equals'          => 'گزینه :attribute باید برابر با تاریخ :date باشد.',
    'date_format'          => 'گزینه :attribute باید با فرمت :format ارسال شود',
    'different'            => 'گزینه :attribute  و :other نباید برابر باشند.',
    'digits'               => ':attribute باید برابر :digits کاراکتر شماره باشد.',
    'digits_between'       => ':attribute باید بین :min و :max شماره باشد',
    'dimensions'           => 'ابعاد تصویر :attribute صحیح نیست ',
    'distinct'             => 'گزینه :attribute مقدار تکراری دارد',
    'email'                => ':attribute باید یک آدرس ایمیل صحیح باشد',
    'ends_with'            => 'The :attribute must end with one of the following: :values.',
    'exists'               => ':attribute انتخاب شده از قبل وجود دارد',
    'file'                 => ':attribute باید یک فایل باشد',
    'filled'               => ':attribute نباید خالی باشد',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'image'                => ':attribute باید یک فایل عکس باشد',
    'in'                   => ':attribute انتخاب شده اشتباه است',
    'in_array'             => ':attribute انتخاب شده بین گزینه های :other وجود ندارد',
    'integer'              => ':attribute باید یک عدد صحیح باشد',
    'ip'                   => ':attribute باید یک آدرس IP صحیح باشد',
    'ipv4'                 => ':attribute باید یک آدرس IP ورژن 4 (IPv4) صحیح باشد',
    'ipv6'                 => ':attribute باید یک آدرس IP ورژن 6 (IPv6) صحیح باشد',
    'json'                 => ':attribute باید به صورت یک json ارسال شود',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'password'             => 'The password is incorrect.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => ':attribute الزامی است.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values are present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'starts_with'          => 'The :attribute must start with one of the following: :values.',
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => ':attribute قبلا استفاده شده است',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',
    'uuid'                 => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
