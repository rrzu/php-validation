Validation
======================================================

用于验证数据的PHP独立库。适用于任何框架

## Features

* 类似Laravel验证的API。
* 数组验证。
* 支持多个文件的`$_FILES`验证。
* 自定义属性别名。
* 自定义验证消息。
* 自定义规则。

## Requirements

* php >= 7.0
* Composer for installation

## Quick Start

#### Installation

```
composer require "rrzu/validation"
```

#### Usage
验证器层
验证器目录结构
```text
├── Service
├── Validator
    ├── CodeMerch
        ├── CodeMerchNoticeValidator
```

创建验证器类
创建一个独立的数据验证器类，用于处理相应场景下的数据验证逻辑。命名规则为***Validator，并继承自AbstractValidator。在该验证器类中，按照命名规则规定命名验证规则方法，例如，对于名为DemoValidator的验证器类，应包括rulesWhenCreate、rulesWhenUpdate...等方法。
```php
/**
 * 测试验证器
 *
 */

namespace common\Validator;

use RRZU\Validation\Validators\AbstractValidator;

class DemoValidator extends AbstractValidator
{
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
          'username' => 'required|string|min:5',
          'page' => 'numeric|default:5',
       	 ],
      ];
  }

  /**
   * 默认自定义错误消息
   *
   * @return array
   */
  public function messages(): array
  {
    return [
        'required' => '该:attribute必填',
      ];
  }


  /**
   * 默认自定义属性名称
   *
   * @return array
   */
  public function attributes(): array
  {
    return [
        'username' => '用户名',
      ];
  }
}
```


语言切换
在组件中，默认采用中文作为消息提示的语言。然而，如果需要切换为英文错误提示，可以通过将语言设置为"en"来实现。
例如，英文提示可能类似于："The 用户名 is required"，而中文提示则为："用户名是必填的"。

```php

class DemoValidator extends AbstractValidator
{
  
  public $language = 'en';

  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
            'name'                  => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:6',
            'confirm_password'      => 'required|same:password',
            'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
            'skills'                => 'array',
            'skills.*.id'           => 'required|numeric',
            'skills.*.percentage'   => 'required|numeric'
      ]
  }
}
  
```


全局错误消息、属性名称
为了提高代码的复用性和可维护性，建议在验证器的验证方法中共用一个messages和attributes变量。这样做可以使得验证方法更通用、易维护。
将:attribute理解为变量，并将其替换为属性或属性别名，使得验证方法更通用、易维护。
如：当验证username参数时，那么验证错误提示：该用户名必填。
```php
class DemoValidator extends AbstractValidator
{

  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
          'username' => 'required|string|min:5',
          'page' => 'numeric|default:5',
          ],
      ];
  }

  public function rulesWhenUpdate()
  {
    return [
        'rules' => [
            'username' => 'required|string|min:5',
            'page' => 'numeric|default:5',
        	],
      ];
  }

  /**
   * 默认自定义错误消息
   *
   * @return array
   */
  public function messages(): array
  {
    return [
        'required' => '该:attribute必填。',
      ];
  }
  
  /**
   * 默认自定义属性名称
   *
   * @return array
   */
  public function attributes(): array
  {
    return [
        'username' => '用户名',
      ];
  }

}
```

局部消息、属性
为了增强验证器的扩展能力，考虑到业务可能存在相同字段需要不同提示的情况，建议引入局部消息和属性的概念。这样可以按照优先级进行提示选择，优先使用局部消息和属性，其次是全局设定，最后是组件默认。
验证消息和属性的等级顺序为：局部 > 全局 > 组件默认。如果定义了局部消息或属性，则优先采用局部定义。
如：当验证username参数时，那么验证错误提示：该商家名称参数缺少。

```php
class DemoValidator extends AbstractValidator
{
  public function rulesWhenCreate(): array
  {
    // 局部  messages 和 attributes
    return [
        'rules' => [
            'username' => 'required|string|min:5',
            'page' => 'numeric|default:5',
        ],
        'messages' => [
        		'required' => '该:attribute参数缺少',
        ],
        'attributes' => [
         	 'username' => '商家名称',
        ],
      ];
  }

  public function rulesWhenUpdate()
  {
    // 局部  messages 和 attributes
    return [
        'rules' => [
            'username' => 'required|string|min:5',
            'page' => 'numeric|default:5',
        ],
        'messages' => [
            'required' => '该:attribute必填',
        ],
        'attributes' => [
            'username' => '用户',
        ],
      ];
  }

  /**
   * 默认自定义错误消息 【全局】
   *
   * @return array
   */
  public function messages(): array
  {
    return [
    		  'required' => '该:attribute必填',
      ];
  }
  
  /**
   * 默认自定义属性名称 【全局】
   *
   * @return array
   */
  public function attributes(): array
  {
    return [
          'username' => '用户名',
      ];
  }

}
```


在控制器中使用验证器
在控制器中调用验证器类进行数据验证：
使用业务验证器DemoValidator进行验证时，可以通过调用validate方法，传入参数$param进行数据验证。对于create操作，底层调用验证器的rulesWhenCreate方法进行验证。
```php
class DemoController extends Controller
{
  public function actionCreate()
  {
    $param = Yii::$app->request->post();

    DemoValidator::create()->validate('create', $param);

    (new DemoService())->create($param);

    // Proceed with user creation logic
    return $this->asJson(['success' => true]);
  }


  public function actionUpdate()
  {
    $param = Yii::$app->request->post();

    DemoValidator::create()->validate('update', $param);

    (new DemoService())->update($param);

    // Proceed with user creation logic
    return Response::successMsg(['success' => true]);
  }
}
```
强制报错
验证器DemoValidator默认采用强制报错机制，框架会全局捕获这些错误并进行处理，返回符合API报错格式的错误信息。这种设计有助于提高整体规范性，降低维护成本，减少开发人员的工作量，避免重复编写相似的错误处理代码。
全局报错目前尚未处理
```php 
 public function actionUpdate()
  {
    $param = Yii::$app->request->post();

    DemoValidator::create()->validate('update', $param);

    (new DemoService())->update($param);

    // Proceed with user creation logic
    return Response::successMsg(['success' => true]);
  }
```

非强制报错
除了默认的强制报错机制，我们还考虑了组件兼容非强制报错的情况。我们对validate方法进行了优化，允许传入参数false，这样开发人员可以通过调用firstErrorMessage方法获取到验证错误信息中的第一条报错信息，实现了更灵活的错误处理方式。
```php
 public function actionUpdate()
  {
    $param = Yii::$app->request->post();

    DemoValidator::create()->validate('update', $param, false);
    
    if ($validate->getValidator()->fails()) {
       return Response::error(422, $validate->getValidator()->firstErrorMessage());
    }
    
    (new DemoService())->update($param);

    // Proceed with user creation logic
    return Response::successMsg(['success' => true]);
  }
```

获取已验证数据、有效数据或无效数据
```php
 public function rulesWhenUpdate()
  {
    return [
        'rules' => [
            'title' => 'Lorem Ipsum',
            'body' => 'Lorem ipsum dolor sit amet ...',
            'published' => null,
            'something' => '-invalid-'
        ]
      ];
  }



  $validate = $validate->validate('update', $data);

  // 获取已验证的数据
  $res = $validate->getValidatedData();
  // [
  //     'title' => 'Lorem Ipsum',
  //     'body' => 'Lorem ipsum dolor sit amet ...',
  //     'published' => '1' // notice this
  //     'something' => '-invalid-'
  // ]

  // 获取有效数据
  $res = $validate->getValidData();
  // [
  //     'title' => 'Lorem Ipsum',
  //     'body' => 'Lorem ipsum dolor sit amet ...',
  //     'published' => '1'
  // ]

  // 获取无效数据
  $res = $validate->getInvalidData();
  // [
  //     'something' => '-invalid-'
  // ]
```

## 验证器自定义消息
### 自定义验证消息
为了提高验证器的灵活性和可定制性，我们引入了两个重要方法。首先是attributes方法，通过该方法，我们可以定义属性别名，将属性名映射为更具有业务意义的别名。其次是messages方法，允许我们自定义字段验证消息，以替换组件默认的消息。在自定义消息中，我们可以使用:attribute占位符，该占位符会被替换为属性别名，以使错误信息更具有可读性和业务意义。这样的设计能够使验证器更符合特定项目需求，同时提高代码的可维护性和易用性。
```php
/**
 * 默认自定义错误消息
 *
 * @return array
 */
  public function messages(): array
  {
    return [
        'required' => '该:attribute必填',
      ];
  }

/**
 * 默认自定义属性名称
 *
 * @return array
 */
  public function attributes(): array
  {
    return [
        'username' => '用户名',
      ];
  }
```

### 特定属性规则的自定义消息
有时，您可能只想为特定字段指定自定义错误信息。您可以属性名称后使用:标记来实现。例如：
```php
/**
 * 默认自定义错误消息
 *
 * @return array
 */
  public function messages(): array
  {
    return [
        'username:required' => '该用户名是必填',
      ];
  }
```

### 数组的属性值判断
验证表单的输入为数组的字段也不再难了。 你可以使用.方法来验证数组中的属性。例如
```php 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
            'skills.*.id'           => 'required|numeric',
            'skills.*.percentage'   => 'required|numeric'
      ]
  }
}
```


## 规则
### required
此验证下的字段必须存在且不能为“空”。

e.g

|Value|	Valid|
|---|---|
|'something'|	true|
|'0'|	true|
|0	|true|
|[0]	|true|
|[null]|	true|
|null|	false|
|[]|	false|
|''	|false|

### required_if
required_if：值_1，值_2，...

如果字段等于任何值，则此规则下的字段必须存在且不为空。
```php
// 当 is_companion 等于 是，则 companion_card 为必须，
// 当 is_companion 等于 否，则 companion_card 为非必须
'is_companion' => 'string|in:是,否',
'companion_card' => 'required_if:is_companion,是|string',
```


### required_unless

required_unless：值_1，值_2，...

如果字段的值不等于任何 value 值，则验证的字段必须存在且不为空。这也意味着，除非字段等于任何 value 值，否则必须在请求数据中包含字段。如果 value 的值为 null （required_unless:name,null），则必须验证该字段，除非比较字段是 null 或比较字段不存在于请求数据中。
```php
// 当 is_companion 等于 是，则 companion_card 为必须，
// 当 is_companion 等于 否，则 companion_card 为非必须
'is_companion' => 'string|in:是,否',
'companion_card' => 'required_if:is_companion,否|string',
```

### required_with
required_with:foo,bar,…

仅当任何其他指定字段存在且不为空时，才需要验证字段存在且不为空。
```php
// 当actors数组存在值时，code 必须存在
'actors' => 'required|array|min:1',
'actors.*.code' => 'required_with:actors|string|min:1',
```

### required_with_all

required_with_all:foo,bar,…

仅当所有其他指定字段存在且不为空时，才需要验证字段存在且不为空。
```php 
'actors' => 'required|array|min:1',
'is_companion' => 'required|array|min:1',
'companion_card' => 'required_with_all:actors,is_companion|string|min:1',
```
### required_without
required_without:foo,bar,…

验证的字段仅在任一其他指定字段为空或不存在时，必须存在且不为空。

### required_without_all
required_without_all:foo,bar,…

验证的字段仅在所有其他指定字段为空或不存在时，必须存在且不为空。

### uploaded_file
uploaded_file：min_size,max_size,extension_a,extension_b,...

该规则将验证来自$_FILES的数据。符合此规则的字段必须遵循以下规则才能被视为有效：

$_FILES['key']['error'] 必须是 UPLOAD_ERR_OK 或 UPLOAD_ERR_NO_FILE。对于UPLOAD_ERR_NO_FILE,您可以使用 required 规则进行验证。
如果指定了最小尺寸，上传的文件尺寸必须不小于最小尺寸。
如果指定了最大尺寸，上传的文件尺寸必须不大于最大尺寸。
如果给定文件类型，MIME 类型必须是给定类型之一。
以下是一些示例定义和解释：

uploaded_file: 上传文件是可选的。当它不为空时，它必须为 ERR_UPLOAD_OK。
required|uploaded_file: 上传文件是必需的，必须为 ERR_UPLOAD_OK。
uploaded_file:0,1M: 上传文件尺寸必须介于 0 到 1 MB 之间，但上传文件是可选的。
required|uploaded_file:0,1M,png,jpeg: 上传文件尺寸必须介于 0 到 1MB，MIME 类型必须是 image/jpeg 或 image/png。
另外，如果您想在尺寸和类型验证之间有不同的错误消息，您可以使用 mimes 规则验证文件类型，并使用 min、max 或 between 验证其尺寸。

对于多文件上传，PHP 将为您提供不理想的数组 $_FILES 结构（这里是主题）。因此，我们制定了 uploaded_file 规则，以自动将您的 $_FILES 值解析为良好组织的数组结构。这意味着，您不能仅使用 min、max、between 或 mimes 规则来验证多文件上传。您应该仅将 uploaded_file 放置在其中以解析其值并确保该值是正确的上传文件值。

例如，如果您有如下输入文件：
```html
<input type="file" name="photos[]"/>
<input type="file" name="photos[]"/>
<input type="file" name="photos[]"/>
```
您可以简单地像这样进行验证：
```php 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
         'photos.*' => 'uploaded_file:0,2M,jpeg,png'
      ]
  }
```

或者
```php 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
          'photos.*' => 'uploaded_file|max:2M|mimes:jpeg,png'
      ]
  }
```
或者如果您有如下输入文件：
```html
<input type="file" name="images[profile]"/>
<input type="file" name="images[cover]"/>
```
您可以像这样进行验证：
```php 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
  	  'images.*' => 'uploaded_file|max:2M|mimes:jpeg,png',
      ]
  }
```

或者
```php 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
  	     'images.profile' => 'uploaded_file|max:2M|mimes:jpeg,png',
    	     'images.cover' => 'uploaded_file|max:5M|mimes:jpeg,png',
      ]
  }
```
现在当您使用 getValidData() 或 getInvalidData() 时，您将获得良好的数组结构，就像单文件上传一样。

### mimes
mimes:foo,bar,…

验证的文件必须具有与列出的扩展名之一对应的 MIME 类型。
MIME 规则的基本用法
```php
'photo' => 'mimes:jpg,bmp,png'
```
尽管您只需要指定扩展名，但该规则实际上通过读取文件内容并猜测其 MIME 类型来验证文件的 MIME 类型。

default
这是一个特殊规则，它不对任何内容进行验证。它只是在您的属性为空或不存在时为其设置默认值。

例如，如果您有如下验证：

```php
 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
  		'enabled' => 'default:1|required|in:0,1'
    		'published' => 'default:0|required|in:0,1'
      ]
  }


// 调用验证
$data = [];
$validate = $validate->validate('demo', $data);

// 获取有效/默认数据
$valid_data = $validate->getValidData();

$enabled = $valid_data['enabled']; // 1
$published = $valid_data['published']; // 0
```
验证通过，因为我们为 enabled 和 published 设置了默认值为 1 和 0，这是有效的。然后，我们可以获取有效/默认数据。

### email
验证的字段必须符合 e-mail 地址格式。

### uppercase
验证字段必须为大写。

### lowercase
验证的字段必须是小写的。

### json
验证的字段必须是一个有效的 JSON 字符串。

### alpha
待验证字段必须是包含在 \p{L} 和 \p{M} 中的 Unicode 字母字符。
为了将此验证规则限制在 ASCII 范围内的字符（a-z 和 A-Z），你可以为验证规则提供 ascii 选项：
'username' => 'alpha:ascii',

### numeric
需要验证的字段必须是数字类型。
'games' => 'required|numeric',

### alpha_num
被验证的字段必须完全是 Unicode 字母数字字符中的 \p{L}, \p{M} 和 \p{N}。
为了将此验证规则限制在 ASCII 范围内的字符（a-z 和 A-Z），你可以为验证规则提供 ascii 选项：
'username' => 'alpha_num:ascii',

### alpha_dash
被验证的字段必须完全是 Unicode 字母数字字符中的 \p{L}、\p{M}、\p{N}，以及 ASCII 破折号（-）和 ASCII 下划线（_）。
为了将此验证规则限制在 ASCII 范围内的字符（a-z 和 A-Z），你可以为验证规则提供 ascii 选项：
'username' => 'alpha_dash:ascii',

### alpha_spaces
此规则下的字段可能包含字母字符和空格。

### in
in:foo,bar,…
验证字段必须包含在给定的值列表中。由于此规则通常要求你 implode 数组，因此可以使用 Rule::in 方法来流畅地构造规则:
'status' => 'in:1,2,10',

### not_in
not_in:foo,bar,…
验证的字段不能包含在给定值列表中
'status' => 'not_in:3,5',

### min
min:value
验证的字段的值必须大于或等于最小值 value。字符串、数字、数组和文件的处理方式与 size 规则相同。
'status' => 'required|numeric|min:1',

### max
max:value
验证的字段的值必须小于或等于最大值 value。字符串、数字、数组和文件的处理方式与 size 规则相同。
'status' => 'required|numeric|max:10',

### between
between:min,max
待验证字段值的大小必须介于给定的最小值和最大值（含）之间。字符串、数字、数组和文件的计算方式都使用 size 方法。
'status' => 'required|numeric|between:1,10',

### digits
digits:value
验证的整数必须具有确切长度 value 。

### digits_between
digits_between:min,max
验证的整数长度必须在给定的 min 和 max 之间。

### url
验证字段必须为有效的 URL。

### integer
验证的字段必须是一个整数。
'id'=>'nullable|integer|min:1',

### boolean
验证的字段必须可以转换为 Boolean 类型。 可接受的输入为 true, false, 1, 0, 「1」, 和 「0」。
'opened' => 'nullable|boolean',

### ip
验证的字段必须是一个 IP 地址。

### ipv4
验证的字段必须是一个 IPv4 地址。

### ipv6
验证的字段必须是一个 IPv6 地址。

### extension
extension:扩展名A,扩展名B,...
此规则下的字段必须以列出的扩展名之一对应的扩展名结尾。
这对于验证给定路径或 URL 的文件类型非常有用。对于验证上传的文件类型应使用 mimes 规则。

### array
待验证字段必须是有效的 PHP 数组。
当向 array 规则提供附加值时，输入数组中的每个键都必须出现在提供给规则的值列表中。在以下示例中，输入数组中的 admin 键无效，因为它不包含在提供给 array 规则的值列表中：

```php
 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
          'user' => 'array:name,username',
      ]
  }

      

$input = [
    'user' => [
        'name' => 'Taylor Otwell',
        'username' => 'taylorotwell',
        'admin' => true,
    ],
];
```

### same
给定的字段必须与验证的字段匹配。此规则下的字段值必须与另一个字段值相同。
```text
 
  public function rulesWhenCreate(): array
  {
    return [
      'rules' => [
  	   'password' => 'required|min:6',
           'confirm_password' => 'required|same:password',
      ]
  }
```

### regex
regex:pattern
验证的字段必须匹配给定的正则表达式。
在内部，此规则使用 PHP 的 preg_match 函数。指定的模式应遵循 preg_match 所需的相同格式，并且也包括有效的分隔符。例如：
```php 
'email' => 'regex:/^.+@.+$/i',
```

### date
验证字段必须是 strtotime PHP 函数可识别的有效日期。
```php 
'birthday' => 'date:Y-m-d',
```

### accepted
待验证字段必须是 「yes」  ，「on」 ，1 或 true。这对于验证「服务条款」的接受或类似字段时很有用。

### present
需要验证的字段必须存在于输入数据中。

### different
验证的字段值必须与字段 field 的值不同。

### after
验证中的字段必须是给定日期之后的值。日期将被传递给 strtotime PHP 函数中，以便转换为有效的 DateTime .
e.g：
```php
'start_date' => 'required|date|after:tomorrow'
```
你也可以指定另一个要与日期比较的字段，而不是传递要由 strtotime 处理的日期字符串：
```php 
'finish_date' => 'required|date|after:start_date'
```

### before
待验证字段的值对应的日期必须在给定的日期之前。这个日期将被传递给 PHP 函数 strtotime 以便转化为有效的 DateTime 实例。此外，与 after 规则一致，可以将另外一个待验证的字段作为 date 的值。

### callback
您可以使用此规则定义自己的验证规则。无法使用字符串管道注册此规则。要使用此规则，应将闭包放置在规则数组内。
e.g：
```php 
public function rulesWhenCreate()
{
    return [
        'rules' => [
            'even_number' =>  ['required',
                function ($value) {
                    // false = invalid
                    return (is_numeric($value) AND $value % 2 === 0);
                }]
        ],
    ];
}
```

您可以通过返回字符串来设置无效消息。例如，上面的示例可以改为：
```php 
public function rulesWhenCreate()
{
    return [
        'rules' => [
            'even_number' =>  [
                'required',
                function ($value) {
                    if (!is_numeric($value)) {
                        return ":attribute must be numeric.";
                    }
                    if ($value % 2 !== 0) {
                        return ":attribute is not even number.";
                    }
                    // 如果值有效，可以返回 true 或不返回任何内容
                }
           ]
        ],
    ];
}
```
注意：RRZU\Validation\Rules\Callback 实例绑定到您的闭包中。因此，您可以使用 $this 访问规则属性和方法。

### nullable
需要验证的字段可以为 null。
```php
'id'=>'nullable|integer|min:1', 
```
