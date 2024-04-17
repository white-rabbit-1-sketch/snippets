extends Creature

class_name GroundCreature

func _ready():
	creature_horizontal_direction = CreatureDirectionEnum.LEFT
	character_speed = 60
	
	state_machine.add(CreatureStateMoveLeft.new())
	state_machine.add(CreatureStateMoveRight.new())
	state_machine.add(CreatureStateDirectionLeft.new())
	state_machine.add(CreatureStateDirectionRight.new())
