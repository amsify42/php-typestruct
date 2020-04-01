<?php

namespace Amsify42\Tests\TypeStruct;

export typestruct User {
	id: int,
	name: string,
	email: string<email>
}